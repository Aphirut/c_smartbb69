<?php
// [คำแนะนำ] เพิ่ม 3 บรรทัดนี้เพื่อแสดง Error ชั่วคราว (เมื่อใช้งานจริงบน Production Server ควรลบออก)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration settings
ini_set('upload_max_filesize', '8M');
ini_set('memory_limit', '512M');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('IMAGE_WIDTH', 800);

// Start session
session_name("C_Smart");
session_start();

// Authentication check
if (!isset($_SESSION['ss_school_id'])) {
    // หากต้องการให้แสดงหน้า error ก่อน สามารถทำได้ แต่ต้องแน่ใจว่า error_page.php ไม่มี fatal error
    // และไม่ควรใช้ header() หลัง include เพราะอาจเกิด "Headers already sent"
    // การ redirect ทันทีจะปลอดภัยกว่า
    header("Location: index.php");
    exit;
}

// [แก้ไข 1] เพิ่มการตรวจสอบว่ามี GD Library หรือไม่
if (!extension_loaded('gd') || !function_exists('gd_info')) {
    die('Error: GD Library is not installed or enabled. Please contact server administrator.');
}

// Include required files
require_once "./includes/connect.php";
// require_once "./includes/function.php"; // หาก function.php มีฟังก์ชันอื่น ให้เปิดใช้งานบรรทัดนี้

/**
 * [แก้ไข 2] เพิ่มฟังก์ชัน ImageConvert ตัวอย่าง
 * ฟังก์ชันนี้ควรจะอยู่ในไฟล์ ./includes/function.php ของคุณ
 * @param string $ext นามสกุลไฟล์ (jpg, png, gif)
 * @param string $tmp_name ที่อยู่ชั่วคราวของไฟล์ที่อัปโหลด
 * @param int $target_width ความกว้างของภาพที่ต้องการ
 * @return GdImage|false trả về đối tượng ảnh GD hoặc false nếu thất bại
 */
function ImageConvert($ext, $tmp_name, $target_width) {
    list($width, $height) = getimagesize($tmp_name);
    if ($width == 0) return false; // ป้องกันการหารด้วยศูนย์

    $target_height = floor($height * ($target_width / $width));
    $thumb = imagecreatetruecolor($target_width, $target_height);

    // จัดการกับความโปร่งใสของ PNG
    if ($ext == 'png') {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $target_width, $target_height, $transparent);
    }

    switch(strtolower($ext)) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($tmp_name);
            break;
        case 'png':
            $source = imagecreatefrompng($tmp_name);
            break;
        case 'gif':
            $source = imagecreatefromgif($tmp_name);
            break;
        default:
            return false; // ไม่รองรับประเภทไฟล์นี้
    }

    if ($source === false) return false;

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $target_width, $target_height, $width, $height);
    imagedestroy($source);

    return $thumb;
}


class PhotoUploader {
    private $pdo;
    private $school_id;
    private $activity_id;
    private $reporting_id;
    private $width;

    public function __construct($pdo, $school_id, $activity_id, $reporting_id, $width) {
        $this->pdo = $pdo;
        $this->school_id = (int)$school_id;
        $this->activity_id = (int)$activity_id;
        $this->reporting_id = $reporting_id ? (int)$reporting_id : null;
        $this->width = (int)$width;
    }

    public function createReporting() {
        if ($this->reporting_id === null) {
            try {
                $sql = "INSERT INTO reporting (school_id, activity_id) VALUES (:school_id, :activity_id)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'school_id' => $this->school_id,
                    'activity_id' => $this->activity_id
                ]);
                
                $this->reporting_id = $this->pdo->lastInsertId();
            } catch (PDOException $e) {
                throw new Exception("Error creating reporting: " . $e->getMessage());
            }
        }
        return $this->reporting_id;
    }

    public function handleFileUpload($files, $texts) {
        $upload_config = [
            "material" => ["./pictures/material/", 4],
            "activity" => ["./pictures/activity/", 6]
        ];

        foreach ($upload_config as $type => $config) {
            list($upload_dir, $count) = $config;
            // สร้าง directory หากยังไม่มี
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }

            for ($i = 1; $i <= $count; $i++) {
                $file_key = "{$type}{$i}";
                if (isset($files[$file_key]) && $files[$file_key]["error"] == UPLOAD_ERR_OK) {
                    $this->processFile($file_key, $files[$file_key], $upload_dir, $i, $type);
                }
            }
        }

        $this->updatePhotoTexts($texts);
    }

    private function processFile($file_key, $file_data, $upload_dir, $index, $type) {
        $extension = strtolower(pathinfo($file_data["name"], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            throw new Exception("รองรับไฟล์ภาพ jpg, jpeg, png, gif เท่านั้นสำหรับไฟล์: " . htmlspecialchars($file_data["name"]));
        }

        // ใช้ฟังก์ชัน ImageConvert ที่เราสร้างไว้
        $image = ImageConvert($extension, $file_data["tmp_name"], $this->width);
        if ($image === false) {
            throw new Exception("ไม่สามารถประมวลผลไฟล์ภาพได้: " . htmlspecialchars($file_data["name"]));
        }

        // บังคับให้เป็น .jpg เพื่อความสม่ำเสมอ
        $filename = "{$this->school_id}-{$this->activity_id}-{$type}-{$index}.jpg";
        
        if (!imagejpeg($image, $upload_dir . $filename, 85)) { // 85 คือคุณภาพของภาพ
            imagedestroy($image);
            throw new Exception("Failed to save image: " . htmlspecialchars($filename));
        }
        
        imagedestroy($image);
        
        $column_name = "photo_{$type}_{$index}";
        $this->updatePhotoInDatabase($column_name, $filename);
    }

    private function updatePhotoInDatabase($column_name, $filename) {
        try {
            $sql = "UPDATE reporting SET {$column_name} = :filename 
                    WHERE reporting_id = :reporting_id AND school_id = :school_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'filename' => $filename,
                'reporting_id' => $this->reporting_id,
                'school_id' => $this->school_id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database error while updating photo name: " . $e->getMessage());
        }
    }

    private function updatePhotoTexts($texts) {
        if ($this->reporting_id === null) return;

        try {
            $filtered_texts = array_filter($texts, function($value) {
                return $value !== '' && $value !== null;
            });

            if (empty($filtered_texts)) return;

            $set_clauses = [];
            $params = [
                'reporting_id' => $this->reporting_id,
                'school_id' => $this->school_id
            ];

            foreach ($filtered_texts as $key => $value) {
                // key คือ 'material1_text', เราต้องการแค่ 'material1'
                $column_key = str_replace('_text', '', $key); 
                $column = "photo_{$column_key}";
                $placeholder = str_replace('_', '', $column_key); // สร้าง placeholder ที่ไม่มีขีด เช่น material1text

                $set_clauses[] = "{$column}_text = :{$placeholder}"; // สมมติว่าคอลัมน์ใน DB ชื่อ photo_material1_text
                $params[$placeholder] = $value;
            }

            $sql = "UPDATE reporting SET " . implode(", ", $set_clauses) . 
                   " WHERE reporting_id = :reporting_id AND school_id = :school_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Error updating photo texts: " . $e->getMessage());
        }
    }
}

// Main execution
try {
    $activity_id = filter_input(INPUT_POST, 'activity_id', FILTER_VALIDATE_INT);
    $reporting_id = filter_input(INPUT_POST, 'reporting_id', FILTER_VALIDATE_INT);
    
    if ($activity_id) {
        $photo_texts = [];
        $text_fields = [
            'material1_text', 'material2_text', 'material3_text', 'material4_text',
            'activity1_text', 'activity2_text', 'activity3_text', 'activity4_text',
            'activity5_text', 'activity6_text'
        ];

        foreach ($text_fields as $field) {
            // [แก้ไข 4] เปลี่ยน FILTER_SANITIZE_STRING เป็น FILTER_DEFAULT
            // ความปลอดภัยจะถูกจัดการโดย PDO prepared statements อยู่แล้ว
            $value = filter_input(INPUT_POST, $field, FILTER_DEFAULT);
            if ($value !== null && $value !== '') {
                $photo_texts[$field] = $value;
            }
        }

        $uploader = new PhotoUploader(
            $pdo, 
            $_SESSION['ss_school_id'], 
            $activity_id, 
            $reporting_id, 
            IMAGE_WIDTH
        );
        
        $new_reporting_id = $uploader->createReporting();
        $uploader->handleFileUpload($_FILES, $photo_texts);

        header("Location: reporting_in.php?act_id=$activity_id");
        exit;
    }
} catch (Exception $e) {
    // [แก้ไข 3] ปรับปรุง Error Handling ให้ถูกต้อง
    // ไม่ใช้ header() หลัง echo แต่ใช้ JavaScript ในการแจ้งเตือนและ redirect
    header('Content-Type: text/html; charset=utf-8');

    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    
    // ใช้ตัวแปร $activity_id ที่อาจจะถูกตั้งค่าไว้แล้ว ถ้าไม่ ให้เป็นค่าว่าง
    $redirect_url = "reporting_in.php?act_id=" . ($activity_id ?? '');

    echo "<script>
            alert('เกิดข้อผิดพลาด: {$message}');
            window.location.href = '{$redirect_url}';
          </script>";
    exit;
}
?>