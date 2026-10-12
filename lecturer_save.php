<?php
session_name("C_Smart");
session_start();

// Function to redirect with a delay and alert message
function redirect_with_alert($message, $url, $delay = 0) {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
    if (!empty($message)) {
        echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
    }
    echo "<meta http-equiv=\"refresh\" content=\"{$delay};URL={$url}\" />";
    exit; // Stop further script execution
}

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php'; // This might already handle redirection or display an error
    // Fallback redirection if error_page.php doesn't exit
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
} else {
    include "./includes/connect.php";
    include "./includes/function.php"; // For BEYearToADYear

    // --- Get common POST data ---
    $school_id = $_SESSION['ss_school_id'];
    $lecturer_id = isset($_POST['lecturer_id']) ? trim($_POST['lecturer_id']) : '';
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $idcard = isset($_POST['idcard']) ? str_replace('-', '', trim($_POST['idcard'])) : ''; // Clean ID card
    $birthday_be = isset($_POST['birthday']) ? trim($_POST['birthday']) : '';
    $education_id = isset($_POST['education_id']) && $_POST['education_id'] !== '' ? $_POST['education_id'] : null;
    $career_id = isset($_POST['career_id']) && $_POST['career_id'] !== '' ? $_POST['career_id'] : null;
    $tel = isset($_POST['tel']) ? str_replace('-', '', trim($_POST['tel'])) : ''; // Clean telephone
    $register_be = isset($_POST['register']) ? trim($_POST['register']) : '';
    $talent = isset($_POST['talent']) ? trim($_POST['talent']) : '';

    // --- Get bank_name and Account_Number ---
    $bank_name = isset($_POST['bank_name']) && $_POST['bank_name'] !== '' ? trim($_POST['bank_name']) : null;
    $Account_Number = isset($_POST['Account_Number']) && $_POST['Account_Number'] !== '' ? trim($_POST['Account_Number']) : null;
    
    // --- Get address components ---
    $house_number = isset($_POST['house_number']) ? trim($_POST['house_number']) : '';
    // village_no เป็น tinyint ต้องส่งเป็น NULL เมื่อไม่ได้กรอก (ค่าว่างจะทำให้ MySQL error)
    $village_no = isset($_POST['village_no']) && trim($_POST['village_no']) !== '' ? trim($_POST['village_no']) : null;
    $village_name = isset($_POST['village_name']) ? trim($_POST['village_name']) : '';
    $subdistrict = isset($_POST['subdistrict']) ? trim($_POST['subdistrict']) : '';
    $district = isset($_POST['district']) ? trim($_POST['district']) : '';
    $province = isset($_POST['province']) ? trim($_POST['province']) : '';
    $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
    $branch = isset($_POST['branch']) && $_POST['branch'] !== '' ? trim($_POST['branch']) : null;

    // --- Combine address components into lecturer_address ---
    // ใช้ addr_format_part() จาก includes/function.php เพื่อไม่ให้คำนำหน้าซ้อนกัน
    // เช่น เลือกจาก dropdown มาเป็น "อำเภอบ้านบึง" จะได้ "อ.บ้านบึง" ไม่ใช่ "อ.อำเภอบ้านบึง"
    $village_part = ($village_no !== null && $village_no !== '') ? 'หมู่ ' . $village_no : '';
    if ($village_name !== '') {
        $village_part = trim($village_part . ' ' . $village_name);
    }

    $address_parts = array_filter([
        $house_number,
        $village_part,
        addr_format_part($subdistrict, 'ต.', ['ตำบล', 'แขวง', 'ต.'], ['แขวง']),
        addr_format_part($district, 'อ.', ['อำเภอ', 'เขต', 'อ.'], ['เขต']),
        addr_format_part($province, 'จ.', ['จังหวัด', 'จ.'], ['กรุงเทพมหานคร']),
        $postal_code
    ], function($part) { return !empty($part); });
    $lecturer_address = implode(' ', $address_parts);

    // --- Convert dates ---
    $birthday_ad = !empty($birthday_be) && function_exists('BEYearToADYear') ? BEYearToADYear($birthday_be) : null;
    $register_ad = !empty($register_be) && function_exists('BEYearToADYear') ? BEYearToADYear($register_be) : null;

    // Basic validation: idcard, firstname, and school_id are required
    if (empty($idcard) || empty($firstname) || empty($school_id)) {
        redirect_with_alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน (เลขบัตรประชาชน, ชื่อ)', 'lecturer_main.php'); // Or back to form
    }

    try {
        // Check for existing ID card (for both insert and update scenarios to prevent duplicates)
        $checkSql = "SELECT lecturer_id FROM lecturer WHERE school_id = :school_id AND idcard = :idcard";
        if (!empty($lecturer_id)) { // If updating, exclude the current lecturer_id from the check
            $checkSql .= " AND lecturer_id != :lecturer_id_exclude";
        }
        $stmtCheck = $pdo->prepare($checkSql);
        $stmtCheck->bindParam(':school_id', $school_id, PDO::PARAM_INT);
        $stmtCheck->bindParam(':idcard', $idcard, PDO::PARAM_STR);
        if (!empty($lecturer_id)) {
            $stmtCheck->bindParam(':lecturer_id_exclude', $lecturer_id, PDO::PARAM_INT);
        }
        $stmtCheck->execute();
        $duplicateLecturer = $stmtCheck->fetch();

        if ($duplicateLecturer) {
            redirect_with_alert('เลขบัตรประชาชน ' . $idcard . ' นี้ มีอยู่ในระบบแล้วสำหรับโรงเรียนนี้', 'lecturer_main.php'); // Or back to form
        }

        if (empty($lecturer_id)) { // ----- INSERT new lecturer -----
            $sql = "INSERT INTO lecturer (
                        school_id, firstname, lastname, idcard, birthday, 
                        education_id, career_id, lecturer_address, tel, register, 
                        talent, bank_name, Account_Number, branch,
                        house_number, village_no, village_name, subdistrict, district, province, postal_code,
                        create_date 
                    ) VALUES (
                        :school_id, :firstname, :lastname, :idcard, :birthday, 
                        :education_id, :career_id, :lecturer_address, :tel, :register, 
                        :talent, :bank_name, :Account_Number, :branch,
                        :house_number, :village_no, :village_name, :subdistrict, :district, :province, :postal_code,
                        NOW()
                    )";
            $stmt = $pdo->prepare($sql);
            // Bind parameters for INSERT (common ones bound later)

        } else { // ----- UPDATE existing lecturer -----
            $sql = "UPDATE lecturer SET 
                        firstname = :firstname, 
                        lastname = :lastname, 
                        idcard = :idcard, 
                        birthday = :birthday, 
                        education_id = :education_id, 
                        career_id = :career_id, 
                        lecturer_address = :lecturer_address, 
                        tel = :tel, 
                        register = :register, 
                        talent = :talent,
                        bank_name = :bank_name,
                        Account_Number = :Account_Number,
                        branch = :branch,
                        house_number = :house_number,
                        village_no = :village_no,
                        village_name = :village_name,
                        subdistrict = :subdistrict,
                        district = :district,
                        province = :province,
                        postal_code = :postal_code
                    WHERE lecturer_id = :lecturer_id AND school_id = :school_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
        }

        // --- Bind common parameters ---
        $stmt->bindParam(':school_id', $school_id, PDO::PARAM_INT);
        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':idcard', $idcard, PDO::PARAM_STR);
        $stmt->bindParam(':birthday', $birthday_ad, $birthday_ad === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':education_id', $education_id, $education_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':career_id', $career_id, $career_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':lecturer_address', $lecturer_address, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':register', $register_ad, $register_ad === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':talent', $talent, PDO::PARAM_STR);

        // --- Bind bank details ---
        $stmt->bindParam(':bank_name', $bank_name, $bank_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':Account_Number', $Account_Number, $Account_Number === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        // --- Bind additional fields ---
        $stmt->bindParam(':branch', $branch, $branch === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':house_number', $house_number, PDO::PARAM_STR);
        $stmt->bindParam(':village_no', $village_no, $village_no === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':village_name', $village_name, PDO::PARAM_STR);
        $stmt->bindParam(':subdistrict', $subdistrict, PDO::PARAM_STR);
        $stmt->bindParam(':district', $district, PDO::PARAM_STR);
        $stmt->bindParam(':province', $province, PDO::PARAM_STR);
        $stmt->bindParam(':postal_code', $postal_code, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = empty($lecturer_id) ? 'เพิ่มข้อมูลวิทยากรสำเร็จ' : 'แก้ไขข้อมูลวิทยากรสำเร็จ';
            redirect_with_alert($message, 'lecturer_main.php');
        } else {
            $errorInfo = $stmt->errorInfo();
            $errorMessage = isset($errorInfo[2]) ? $errorInfo[2] : 'ไม่สามารถบันทึกข้อมูลได้';
            error_log("SQL Execution Error: " . $errorMessage . " | SQL: " . $sql); // Log the SQL too
            redirect_with_alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $errorMessage, 'lecturer_main.php'); // Or back to form
        }

    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage() . " | SQL: " . (isset($sql) ? $sql : 'N/A')); // Log the SQL if available
        redirect_with_alert('เกิดข้อผิดพลาดร้ายแรงในระบบฐานข้อมูล: ' . $e->getMessage(), 'lecturer_main.php'); // Or back to form
    }
}
?>