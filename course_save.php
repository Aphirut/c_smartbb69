<?php
session_name("C_Smart");
session_start();

// 1. ตรวจสอบการล็อกอินและ session
if (!isset($_SESSION['ss_school_id']) || $_SESSION['ss_login_status'] != 1) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit(); // หยุดการทำงานของสคริปต์ทันที
}

echo "<META HTTP-EQUIV='Content-Type' content='text/html; charset=utf-8' />";
include "./includes/connect.php";

// 2. รับค่าจากฟอร์มและ Session
// IMPROVEMENT: ใช้ school_id จาก session เพื่อความปลอดภัยและถูกต้อง
$school_id = $_SESSION['ss_school_id']; 
$course_name = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';
$culture_id = isset($_POST['culture_id']) ? $_POST['culture_id'] : '';
$course_hour = isset($_POST['course_hour']) ? trim($_POST['course_hour']) : '';

// 3. ตรวจสอบข้อมูลเบื้องต้นและไฟล์ที่อัปโหลด
// IMPROVEMENT: ตรวจสอบการมีอยู่ของไฟล์และ error code
if (
    !empty($course_name) && 
    !empty($culture_id) && 
    !empty($course_hour) && 
    isset($_FILES["fileToUpload"]) && 
    $_FILES["fileToUpload"]["error"] == 0
) {
    $target_dir = "./courses/";
    $original_filename = basename($_FILES["fileToUpload"]["name"]);
    $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // 4. ตรวจสอบว่าเป็นไฟล์ PDF หรือไม่
    if ($file_extension == "pdf") {
        try {
            // 5. ตรวจสอบว่ามีหลักสูตรนี้ในระบบแล้วหรือยัง
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM course WHERE course_name = :course_name AND course_hour = :course_hour AND school_id = :school_id");
            $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
            $stmt->bindParam(':course_hour', $course_hour, PDO::PARAM_STR);
            $stmt->bindParam(':school_id', $school_id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // --- FIX: สร้างชื่อไฟล์ใหม่ที่ปลอดภัยและสอดคล้องกัน โดยไม่ใช้ iconv ---
                // ใช้ค่าที่รับมาสร้างชื่อไฟล์โดยตรง (ระบบไฟล์ส่วนใหญ่รองรับ UTF-8)
                $new_filename = $culture_id . "-" . $course_name . "-" . $course_hour . "-ชั่วโมง.pdf";
                
                // path ที่จะใช้ในการย้ายไฟล์
                $destination_path = $target_dir . $new_filename;

                // 6. ย้ายไฟล์ไปยังตำแหน่งที่ต้องการ
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $destination_path)) {

                    // 7. บันทึกข้อมูลลงฐานข้อมูลโดยใช้ชื่อไฟล์ใหม่ที่สร้างขึ้น
                    $sql = "INSERT INTO course (course_name, culture_id, course_hour, course_file, school_id) 
                            VALUES (:course_name, :culture_id, :course_hour, :course_file, :school_id)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
                    $stmt->bindParam(':culture_id', $culture_id, PDO::PARAM_INT);
                    $stmt->bindParam(':course_hour', $course_hour, PDO::PARAM_STR);
                    // ใช้ $new_filename ทั้งในการบันทึกไฟล์และลง DB
                    $stmt->bindParam(':course_file', $new_filename, PDO::PARAM_STR); 
                    $stmt->bindParam(':school_id', $school_id, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        // FIX: Redirect เมื่อสำเร็จเท่านั้น
                        echo "<script>
                                alert('บันทึกข้อมูลหลักสูตรสำเร็จ');
                                window.location='course_show.php?culture_id=$culture_id';
                              </script>";
                        exit();
                    } else {
                        // กรณี insert ไม่สำเร็จ ให้ลบไฟล์ที่อัปโหลดไปแล้วออก
                        unlink($destination_path); 
                        echo "<script>
                                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล');
                                window.location='course_in.php';
                              </script>";
                        exit();
                    }

                } else {
                    echo "<script>
                            alert('อัปโหลดไฟล์ล้มเหลว กรุณาลองอีกครั้ง');
                            window.location='course_in.php';
                          </script>";
                    exit();
                }
            } else {
                echo "<script>
                        alert('มีหลักสูตรชื่อนี้และจำนวนชั่วโมงนี้ในระบบแล้ว');
                        window.location='course_in.php';
                      </script>";
                exit();
            }

        } catch (PDOException $e) {
            // error_log("PDO Exception: " . $e->getMessage()); // ควรเปิดใช้บน server จริง
            echo "<script>
                    alert('เกิดข้อผิดพลาดกับฐานข้อมูล: " . addslashes($e->getMessage()) . "');
                    window.location='course_in.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('กรุณาอัปโหลดไฟล์นามสกุล .pdf เท่านั้น');
                window.location='course_in.php';
              </script>";
        exit();
    }
} else {
    // กรณีข้อมูลไม่ครบหรือไม่ไฟล์ถูกส่งมา
    echo "<script>
            alert('กรุณากรอกข้อมูลให้ครบถ้วนและแนบไฟล์หลักสูตร');
            window.location='course_in.php';
          </script>";
    exit();
}
?>