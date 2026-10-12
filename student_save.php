<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";
    include "./includes/function.php";

    $school_id = $_SESSION['ss_school_id'];
    $student_id = $_POST['student_id'] ?? ''; // Using the null coalescing operator to handle undefined keys
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $sex_id = $_POST['sex_id'] ?? '';
    $idcard = $_POST['idcard'] ?? '';
    $birthday = isset($_POST['birthday']) ? BEYearToADYear($_POST['birthday']) : '';
    $education_id = $_POST['education_id'] ?? '';
    $career_id = $_POST['career_id'] ?? '';
    $target_id = $_POST['target_id'] ?? '';
    $income = str_replace(',', '', $_POST['income'] ?? '0');
    $address = $_POST['address'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $nationality_id = $_POST['nationality_id'] ?? '';
    $religion_id = $_POST['religion_id'] ?? '';
    $register = isset($_POST['register']) ? BEYearToADYear($_POST['register']) : '';

    // แปลงค่าว่างของฟิลด์ integer เป็น NULL (MySQL strict mode ไม่รับ '' ในคอลัมน์ int)
    $sex_id = ($sex_id === '') ? null : $sex_id;
    $education_id = ($education_id === '') ? null : $education_id;
    $career_id = ($career_id === '') ? null : $career_id;
    $target_id = ($target_id === '') ? null : $target_id;
    $income = ($income === '') ? 0 : $income;

    // สัญชาติ/ศาสนา ใน DB เป็น NOT NULL — หากไม่ได้เลือก ใช้ค่าเริ่มต้น (ไทย / พุทธ)
    try {
        if ($nationality_id === '') {
            $nStmt = $pdo->query("SELECT nationality_id FROM nationality WHERE nationality_name = 'ไทย' LIMIT 1");
            $nationality_id = $nStmt->fetchColumn();
            if ($nationality_id === false) {
                $nationality_id = $pdo->query("SELECT nationality_id FROM nationality ORDER BY nationality_id ASC LIMIT 1")->fetchColumn();
            }
        }
        if ($religion_id === '') {
            $rStmt = $pdo->query("SELECT religion_id FROM religion WHERE religion_name LIKE 'พุทธ%' LIMIT 1");
            $religion_id = $rStmt->fetchColumn();
            if ($religion_id === false) {
                $religion_id = $pdo->query("SELECT religion_id FROM religion ORDER BY religion_id ASC LIMIT 1")->fetchColumn();
            }
        }
    } catch (PDOException $e) {
        // ไม่พบค่าเริ่มต้น → ปล่อยตามค่าที่ส่งมา (จะตรวจสอบด้านล่างต่อไป)
    }

    if ($idcard != "" && $firstname != "" && $school_id != "") {
        try {
            // Prepare and execute a SELECT statement to check for existing ID card
            $stmt = $pdo->prepare("SELECT * FROM student WHERE school_id = :school_id AND idcard = :idcard LIMIT 1");
            $stmt->execute(['school_id' => $school_id, 'idcard' => $idcard]);
            $row = $stmt->fetch();
            $numRows = $stmt->rowCount();

            if ($student_id == "") {
                if ($numRows == 0) {
                    // Prepare and execute an INSERT statement if no record found
                    $sql = "INSERT INTO student (
                                school_id, firstname, lastname, sex_id, idcard, birthday, education_id, 
                                career_id, target_id, income, address, tel, nationality_id, religion_id, register
                                ) VALUES (
                                :school_id, :firstname, :lastname, :sex_id, :idcard, :birthday, :education_id,
                                :career_id, :target_id, :income, :address, :tel, :nationality_id, :religion_id, :register)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'school_id' => $school_id,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'sex_id' => $sex_id,
                        'idcard' => $idcard,
                        'birthday' => $birthday,
                        'education_id' => $education_id,
                        'career_id' => $career_id,
                        'target_id' => $target_id,
                        'income' => $income,
                        'address' => $address,
                        'tel' => $tel,
                        'nationality_id' => $nationality_id,
                        'religion_id' => $religion_id,
                        'register' => $register
                    ]);
                    $student_id = $pdo->lastInsertId();
                } else {
                    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                    echo "<script type='text/javascript'>alert('เลขบัตรประชาชนดังกล่าวมีในระบบแล้ว');</script>";
                }
            } else {
                if ($numRows == 0 || ($student_id == $row['student_id'])) {
                    // Prepare and execute an UPDATE statement if record is not found or if updating the same record
                    $sql = "UPDATE student SET 
                                firstname=:firstname, lastname=:lastname, sex_id=:sex_id, idcard=:idcard, 
                                birthday=:birthday, education_id=:education_id, career_id=:career_id, target_id=:target_id, 
                                income=:income, address=:address, tel=:tel, nationality_id=:nationality_id, 
                                religion_id=:religion_id, register=:register 
                                WHERE student_id = :student_id AND school_id = :school_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'sex_id' => $sex_id,
                        'idcard' => $idcard,
                        'birthday' => $birthday,
                        'education_id' => $education_id,
                        'career_id' => $career_id,
                        'target_id' => $target_id,
                        'income' => $income,
                        'address' => $address,
                        'tel' => $tel,
                        'nationality_id' => $nationality_id,
                        'religion_id' => $religion_id,
                        'register' => $register,
                        'student_id' => $student_id,
                         'school_id' => $school_id
                    ]);
                } else {
                    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                    echo "<script type='text/javascript'>alert('เลขบัตรประชาชนดังกล่าวมีในระบบแล้ว');</script>";
                }
            }

            // ================================================================
            // บันทึกข้อมูลแบบ พต.2 (ใบสมัครผู้เรียน) และ พต.1 (แบบสำรวจความต้องการ)
            // เก็บในตาราง student_pt (1:1 กับ student)
            // ================================================================
            if ($student_id != "") {
                // แปลงค่าหลักสูตรที่เลือกจาก dropdown (course_id) กลับเป็นชื่อ/ชั่วโมงจากฐานข้อมูล
                // - เลือกจากรายการ → ใช้ชื่อหลักสูตรจากตาราง course (กันชื่อเพี้ยน), ชั่วโมงใช้ค่าที่กรอก (JS เติมให้อัตโนมัติแล้ว)
                // - เลือก "อื่น ๆ" → ใช้ข้อความที่พิมพ์เอง
                // - ไม่มี dropdown (สถานศึกษาไม่มีหลักสูตร) → ใช้ค่าข้อความเดิม
                $pt2CourseName = trim($_POST['pt2_course_name'] ?? '');
                $pt2CourseSel = trim($_POST['pt2_course_select'] ?? '');
                if ($pt2CourseSel === 'other') {
                    $pt2CourseName = trim($_POST['pt2_course_custom'] ?? '');
                } elseif ($pt2CourseSel !== '' && ctype_digit($pt2CourseSel)) {
                    $cLookup = $pdo->prepare("SELECT course_name, course_hour FROM course WHERE course_id = :cid LIMIT 1");
                    $cLookup->execute(['cid' => (int)$pt2CourseSel]);
                    if ($cRow = $cLookup->fetch(PDO::FETCH_ASSOC)) {
                        $pt2CourseName = $cRow['course_name'];
                        if (($_POST['pt2_course_hours'] ?? '') === '' && $cRow['course_hour'] !== null) {
                            $_POST['pt2_course_hours'] = $cRow['course_hour'];
                        }
                    }
                }
                $ptData = [
                    // พต.2
                    'pt2_no'              => trim($_POST['pt2_no'] ?? ''),
                    'pt2_course_name'     => $pt2CourseName,
                    'pt2_course_hours'    => ($_POST['pt2_course_hours'] ?? '') === '' ? null : (int)$_POST['pt2_course_hours'],
                    'pt2_interests'       => isset($_POST['pt2_interests']) && is_array($_POST['pt2_interests']) ? implode(',', array_map('intval', $_POST['pt2_interests'])) : null,
                    'pt2_interest_other'  => trim($_POST['pt2_interest_other'] ?? ''),
                    'pt2_statuses'        => isset($_POST['pt2_statuses']) && is_array($_POST['pt2_statuses']) ? implode(',', array_map('intval', $_POST['pt2_statuses'])) : null,
                    'pt2_status_other'    => trim($_POST['pt2_status_other'] ?? ''),
                    'pt2_learn_method'    => ($_POST['pt2_learn_method'] ?? '') === '' ? null : (int)$_POST['pt2_learn_method'],
                    'pt2_learn_place'     => trim($_POST['pt2_learn_place'] ?? ''),
                    'pt2_learn_course'    => trim($_POST['pt2_learn_course'] ?? ''),
                    'pt2_news_source'     => trim($_POST['pt2_news_source'] ?? ''),
                    // พต.1
                    'pt1_has_career'      => isset($_POST['pt1_has_career']) && $_POST['pt1_has_career'] !== '' ? (int)$_POST['pt1_has_career'] : null,
                    'pt1_career_detail'   => trim($_POST['pt1_career_detail'] ?? ''),
                    'pt1_certificates'    => trim($_POST['pt1_certificates'] ?? ''),
                    'pt1_need_career'     => isset($_POST['pt1_need_career']) && is_array($_POST['pt1_need_career']) ? implode(',', array_map('intval', $_POST['pt1_need_career'])) : null,
                    'pt1_need_career_other' => trim($_POST['pt1_need_career_other'] ?? ''),
                    'pt1_need_career_hours' => ($_POST['pt1_need_career_hours'] ?? '') === '' ? null : (int)$_POST['pt1_need_career_hours'],
                    'pt1_need_career_hours_other' => trim($_POST['pt1_need_career_hours_other'] ?? ''),
                    'pt1_need_life'       => isset($_POST['pt1_need_life']) && is_array($_POST['pt1_need_life']) ? implode(',', array_map('intval', $_POST['pt1_need_life'])) : null,
                    'pt1_need_life_other' => trim($_POST['pt1_need_life_other'] ?? ''),
                    'pt1_need_life_hours' => ($_POST['pt1_need_life_hours'] ?? '') === '' ? null : (int)$_POST['pt1_need_life_hours'],
                    'pt1_need_life_hours_other' => trim($_POST['pt1_need_life_hours_other'] ?? ''),
                    'pt1_need_social'     => isset($_POST['pt1_need_social']) && is_array($_POST['pt1_need_social']) ? implode(',', array_map('intval', $_POST['pt1_need_social'])) : null,
                    'pt1_need_social_other' => trim($_POST['pt1_need_social_other'] ?? ''),
                    'pt1_need_social_hours' => ($_POST['pt1_need_social_hours'] ?? '') === '' ? null : (int)$_POST['pt1_need_social_hours'],
                    'pt1_need_social_hours_other' => trim($_POST['pt1_need_social_hours_other'] ?? ''),
                    'pt1_learn_days'      => isset($_POST['pt1_learn_days']) && is_array($_POST['pt1_learn_days']) ? implode(',', array_map('intval', $_POST['pt1_learn_days'])) : null,
                    'pt1_learn_time'      => (function () {
                        $s = $_POST['pt1_learn_start'] ?? '';
                        $e = $_POST['pt1_learn_end'] ?? '';
                        $ok = '/^\d{2}:\d{2}$/';
                        return (preg_match($ok, $s) && preg_match($ok, $e) && $e > $s) ? "$s - $e" : '';
                    })(),
                    'pt1_goals'           => isset($_POST['pt1_goals']) && is_array($_POST['pt1_goals']) ? implode(',', array_map('intval', $_POST['pt1_goals'])) : null,
                    'pt1_goal_other'      => trim($_POST['pt1_goal_other'] ?? ''),
                ];

                $ptColumns = array_keys($ptData);
                $allColumns = array_merge(['student_id', 'school_id'], $ptColumns);

                $colSql = implode(', ', array_map(fn($c) => "`$c`", $allColumns));
                $valSql = implode(', ', array_map(fn($c) => ":$c", $allColumns));
                // PDO (emulation off) ไม่อนุญาตให้ใช้ named parameter ซ้ำใน statement เดียว
                // จึงต้องใช้ชื่อพารามิเตอร์แยกสำหรับส่วน ON DUPLICATE KEY UPDATE
                $updSql = implode(', ', array_map(fn($c) => "`$c` = :upd_$c", $ptColumns));

                $sql = "INSERT INTO student_pt ($colSql)
                        VALUES ($valSql)
                        ON DUPLICATE KEY UPDATE $updSql";

                $stmt = $pdo->prepare($sql);
                $params = [];
                foreach ($ptData as $k => $v) {
                    $val = ($v === '') ? null : $v;
                    $params[$k] = $val;
                    $params['upd_' . $k] = $val;
                }
                $params['student_id'] = $student_id;
                $params['school_id'] = $school_id;
                $stmt->execute($params);
            }

        } catch (PDOException $e) {
             echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            echo "เกิดข้อผิดพลาดในการประมวลผล: " . $e->getMessage();
            exit;
        }
    }
    echo "<meta http-equiv=\"refresh\" content=\"0;URL=student_main.php\" />";
}
?>
