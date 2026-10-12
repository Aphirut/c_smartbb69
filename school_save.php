<?php
ini_set('upload_max_filesize', '8M');
ini_set('memory_limit', '512M');

session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";

    $school_id = $_SESSION['ss_school_id'];
    $numbook_amp = $_POST['numbook_amp'];
    $ss_teacher_name = $_POST['teacher_name'];
    $teacher_position_id = $_POST['teacher_position_id'];
    $president_name = $_POST['president_name'];
    $president_position_id = $_POST['president_position_id'];
    $headprocurementer = $_POST['headprocurementer'];
    $procurementer = $_POST['procurementer'];
    $planner = $_POST['planner'];
    $continueter = $_POST['continueter'];
    $registrar = $_POST['registrar'] ?? '';
    $financial = $_POST['financial'];
    $ss_address = $_POST['address'];
    $ss_district = $_POST['district'];
    $ss_amphur = $_POST['amphur'];
    $ss_province = $_POST['province'];
    $ss_postcode = $_POST['postcode'];
    $tel_teacher = $_POST['tel_teacher'];
    $phone = $_POST['phone'];
    $fax = $_POST['fax'];
    $latitude = $_POST['latitude'];
    $longtitude = $_POST['longtitude'];
    $img_ori_name = $_FILES["fileUpload"]["name"];
    
    $new_images = ''; // Initialize outside the if block
    try {
        // Fetch position name using PDO
        $stmt = $pdo->prepare("SELECT position_name FROM position WHERE position_id = :teacher_position_id");
        $stmt->execute(['teacher_position_id' => $teacher_position_id]);
        $row = $stmt->fetch();
        $ss_teacher_position = $row ? $row['position_name'] : '';

		if ($school_id != "") {
            if ($img_ori_name != "") {
                $extension = pathinfo($_FILES["fileUpload"]["name"], PATHINFO_EXTENSION);
                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
                    $images = $_FILES["fileUpload"]["tmp_name"];
                    $new_images = $school_id . "-" . "school-photo" . ".jpg";
                    $width = 400;
                    $size = getimagesize($images);
                    $height = round($width * $size[1] / $size[0]);

                    switch ($extension) {
                        case 'png':
                            $images_orig = imagecreatefrompng($images);
                            break;
                        case 'gif':
                            $images_orig = imagecreatefromgif($images);
                            break;
                        case 'jpg':
                        case 'jpeg':
                            $images_orig = imagecreatefromjpeg($images);
                            break;
                    }

                    $photoX = imagesx($images_orig);
                    $photoY = imagesy($images_orig);
                    $images_fin = imagecreatetruecolor($width, $height);
                    imagecopyresampled($images_fin, $images_orig, 0, 0, 0, 0, $width + 1, $height + 1, $photoX, $photoY);
                    imagejpeg($images_fin, "./pictures/school/" . $new_images);
                    imagedestroy($images_orig);
                    imagedestroy($images_fin);
                } else {
                    echo "<META HTTP-EQUIV='Content-Type' content='text/html; charset=utf-8' />";
                    echo "ไม่สามารถอัพโหลดรูปภาพได้ กรุณาลองอีกครั้ง";
					$new_images = "";
                }
            }
			
            $sql = "UPDATE school SET numbook_amp = :numbook_amp, teacher_name = :teacher_name, 
                    teacher_position_id = :teacher_position_id, president_name = :president_name, 
                    president_position_id = :president_position_id, headprocurementer = :headprocurementer,
                    procurementer = :procurementer, planner = :planner, continueter = :continueter, registrar = :registrar, 
                    financial = :financial, address = :address, district = :district, amphur = :amphur, 
                    province = :province, postcode = :postcode, tel_teacher = :tel_teacher, 
                    phone = :phone, fax = :fax, latitude = :latitude, longtitude = :longtitude";
                    
           if (!empty($new_images)) {
                $sql .= ", photo = :photo";
            }
            $sql .= " WHERE school_id = :school_id";
            
            $stmt = $pdo->prepare($sql);
            
            $params = [
                'numbook_amp' => $numbook_amp,
                'teacher_name' => $ss_teacher_name,
                'teacher_position_id' => $teacher_position_id,
                'president_name' => $president_name,
                'president_position_id' => $president_position_id,
                'headprocurementer' => $headprocurementer,
                'procurementer' => $procurementer,
                'planner' => $planner,
                'continueter' => $continueter,
                'registrar' => $registrar,
                'financial' => $financial,
                'address' => $ss_address,
                'district' => $ss_district,
                'amphur' => $ss_amphur,
                'province' => $ss_province,
                'postcode' => $ss_postcode,
                'tel_teacher' => $tel_teacher,
                'phone' => $phone,
                'fax' => $fax,
                'latitude' => $latitude,
                'longtitude' => $longtitude,
                'school_id' => $school_id,
                
            ];

			if (!empty($new_images)){
				$params['photo'] = $new_images;
			}
            
			$stmt->execute($params);
        }
		echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>