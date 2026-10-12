<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";

    $del_id = isset($_REQUEST['del_id']) ? $_REQUEST['del_id'] : '';

    if ($del_id != "" && is_numeric($del_id)) {
        try {
            // ตรวจสอบว่าผู้เรียนคนดังกล่าวถูกจัดกลุ่มไปแล้วหรือยัง (ในตาราง train)
            $sql_check = "SELECT * FROM train WHERE student_id = :del_id LIMIT 1";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':del_id', $del_id, PDO::PARAM_INT);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {
                // ถ้ายังไม่ได้จัดกลุ่ม ให้ลบข้อมูลผู้เรียน
                $sql_delete = "DELETE FROM student WHERE student_id = :del_id AND school_id = :school_id";
                $stmt_delete = $pdo->prepare($sql_delete);
                $stmt_delete->bindParam(':del_id', $del_id, PDO::PARAM_INT);
                $stmt_delete->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
                $stmt_delete->execute();
            } else {
                echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                echo "<script type='text/javascript'>alert('ไม่สามารถลบผู้เรียนได้ เนื่องจากจัดตั้งกลุ่มแล้ว');</script>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=student_main.php'>";
}
?>