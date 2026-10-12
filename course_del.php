<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id']) || $_SESSION['ss_login_status'] != 1) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    // Include PDO connection
    include "./includes/connect.php"; // Assuming this file contains the PDO connection code you provided earlier

    // Use prepared statements to prevent SQL injection
    $del_id = isset($_REQUEST['del_id']) ? $_REQUEST['del_id'] : null; // Use a ternary operator to handle missing values
    $culture_id = isset($_REQUEST['culture_id']) ? $_REQUEST['culture_id'] : null;

    if (is_numeric($del_id) && $del_id !== null) {
        try {
            // Check if there are related activities
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE course_id = :del_id");
            $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // Delete the course if no related activities are found
                $stmt = $pdo->prepare("DELETE FROM course WHERE course_id = :del_id");
                $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
                $stmt->execute();

            } else {
                // Course cannot be deleted due to related activities
                echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                echo "<script type='text/javascript'>alert('ไม่สามารถลบหลักสูตรได้ เนื่องจากจัดตั้งกลุ่มแล้ว');</script>";
            }
              echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=course_show.php?culture_id=$culture_id'>";

        } catch (PDOException $e) {
            // Handle PDO Exceptions
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
             echo "เกิดข้อผิดพลาดในการดำเนินการ: " . $e->getMessage();
            // Log the error or handle it accordingly
            exit; // Important to exit to prevent further errors
        }

    } else {
        // $del_id was not a number or was null
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            echo "<script type='text/javascript'>alert('ค่า del_id ไม่ถูกต้อง');</script>";
             echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=course_show.php?culture_id=$culture_id'>";
    }



}
?>