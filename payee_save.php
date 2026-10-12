<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php"; // Assuming this file sets $pdo
    include "./includes/function.php";

    $school_id = $_SESSION['ss_school_id'];
    $payee_id = $_POST['payee_id'] ?? null; // Use null coalesce operator to avoid errors if not set
    $payee_name = $_POST['payee_name'] ?? '';
    $taxid = $_POST['taxid'] ?? '';
    $payee_address = $_POST['payee_address'] ?? '';
    $tel = $_POST['tel'] ?? '';

    if ($taxid != "" && $payee_name != "" && $school_id != "") {
        try {
            // Check if Tax ID already exists
            $stmt = $pdo->prepare("SELECT * FROM payee WHERE school_id = :school_id AND taxid = :taxid LIMIT 1");
            $stmt->execute(['school_id' => $school_id, 'taxid' => $taxid]);
            $row = $stmt->fetch();

            if ($payee_id == null) { // Insert New Record
                if (!$row) {
                    $sql = "INSERT INTO payee (school_id, taxid, payee_name, payee_address, tel) 
                            VALUES (:school_id, :taxid, :payee_name, :payee_address, :tel)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'school_id' => $school_id,
                        'taxid' => $taxid,
                        'payee_name' => $payee_name,
                        'payee_address' => $payee_address,
                        'tel' => $tel,
                    ]);
                } else {
                    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                    echo "<script type='text/javascript'>alert('เลขประจำตัวผู้เสียภาษีดังกล่าวมีในระบบแล้ว');</script>";
                }
            } else {  // Update Existing Record
                if (!$row || ($payee_id == $row['payee_id'])) {
                    $sql = "UPDATE payee SET 
                            taxid = :taxid, payee_name = :payee_name, payee_address = :payee_address, tel = :tel 
                            WHERE payee_id = :payee_id AND school_id = :school_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'taxid' => $taxid,
                        'payee_name' => $payee_name,
                        'payee_address' => $payee_address,
                        'tel' => $tel,
                        'payee_id' => $payee_id,
                        'school_id' => $school_id
                    ]);
                } else {
                    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                    echo "<script type='text/javascript'>alert('เลขประจำตัวผู้เสียภาษีดังกล่าวมีในระบบแล้ว');</script>";
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            // You might want to add more specific error handling here.
            // For example, logging errors or displaying a user-friendly message.
            exit;
        }
    }
    echo "<meta http-equiv=\"refresh\" content=\"0;URL=payee_main.php\" />";
}
?>