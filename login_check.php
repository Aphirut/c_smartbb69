<?php
session_name("C_Smart");
session_start();
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
include "./includes/connect.php"; // ไฟล์นี้จะเชื่อมต่อ PDO

// ตรวจสอบว่ามี $pdo ที่ถูกสร้างจากการเชื่อมต่อฐานข้อมูลหรือไม่
if (!isset($pdo)) {
    echo "เกิดข้อผิดพลาด: ไม่พบการเชื่อมต่อฐานข้อมูล PDO";
    exit;
}


$usr_login = filter_input(INPUT_POST, 'username_schoolid', FILTER_SANITIZE_STRING);
$pwd_login = filter_input(INPUT_POST, 'password_schoolid', FILTER_SANITIZE_STRING);

if (empty($usr_login) || empty($pwd_login)) {
    header("Location: login.php?error=2");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM school WHERE school_id = :usr_login AND password = :pwd_login LIMIT 1");
    $stmt->execute([':usr_login' => $usr_login, ':pwd_login' => $pwd_login]);
    $row = $stmt->fetch();

    if ($row) {
		$_SESSION['ss_school_id'] = $row['school_id'];
		$_SESSION['ss_school_name'] = $row['school_name'];
		$_SESSION['ss_teacher_name'] = $row['teacher_name'];
		$_SESSION['ss_district'] = $row['district'];
		$_SESSION['ss_amphur'] = $row['amphur'];
		$_SESSION['ss_province'] = $row['province'];
		$_SESSION['ss_address'] = $row['address'];
		$_SESSION['ss_postcode'] = $row['postcode'];
		$_SESSION['ss_photo'] = $row['photo'];
		$_SESSION['ss_last_login'] = $row['last_login'];
		$_SESSION['ss_login_status'] = $row['status'];

		if ($row['status'] == 3) {
			$stmt_position = $pdo->prepare("SELECT * FROM position WHERE position_id = :teacher_position_id");
			$stmt_position->execute([':teacher_position_id' => $row['teacher_position_id']]);
			$row_position = $stmt_position->fetch();
			$_SESSION['ss_teacher_position'] = $row_position['position_name'];
		}

        $school_ids = substr($_SESSION['ss_school_id'], 0, 6);
        $stmt_amp = $pdo->prepare("SELECT * FROM school INNER JOIN position ON position.position_id = school.president_position_id WHERE school_id LIKE :school_ids AND status = '2'");
        $stmt_amp->execute([':school_ids' => $school_ids . '%']);
		$row_amp = $stmt_amp->fetch();
		
        if ($row_amp) {
			$_SESSION['ss_numbook_amp'] = $row_amp['numbook_amp'];
			$_SESSION['ss_school_amphur'] = str_replace("สกร.อำเภอ", "", $row_amp['school_name']);
			$_SESSION['ss_school_amphur_district'] = $row_amp['district'];
			$_SESSION['ss_school_amphur_postcode'] = $row_amp['postcode'];
			$_SESSION['ss_school_amphur_phone'] = $row_amp['phone'];
			$_SESSION['ss_school_amphur_fax'] = $row_amp['fax'];
			$_SESSION['ss_president_name'] = $row_amp['president_name'];
			$_SESSION['ss_president_position'] = $row_amp['position_name'];
			$_SESSION['ss_procurementer'] = $row_amp['procurementer'];
			$_SESSION['ss_planner'] = $row_amp['planner'];
			$_SESSION['ss_continueter'] = $row_amp['continueter'];
			$_SESSION['ss_registrar'] = $row_amp['registrar'] ?? '';
			$_SESSION['ss_headprocurementer'] = $row_amp['headprocurementer'];
			$_SESSION['ss_financial'] = $row_amp['financial'];
        }

        $date_note = date('Y-m-d H:i:s');
        $stmt_update = $pdo->prepare("UPDATE school SET last_login = :date_note WHERE school_id = :usr_login");
        $stmt_update->execute([':date_note' => $date_note, ':usr_login' => $usr_login]);

		echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
		exit;

    } else {
        header("Location: login.php?error=1");
        exit;
    }

} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage();
}
?>