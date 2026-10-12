<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";
    $del_id = $_REQUEST['del_id'] ?? ''; // Using null coalescing operator

    if ($del_id != "" && is_numeric($del_id)) {
        try {
            // Check if activity exists for the school
            $stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :del_id AND school_id = :school_id LIMIT 1");
            $stmt->execute(['del_id' => $del_id, 'school_id' => $_SESSION['ss_school_id']]);
            
            if ($row = $stmt->fetch()) {
                if ($row['status_line'] == "") {
                     // Start a transaction to ensure atomicity
                    $pdo->beginTransaction();
                   
                    try {
                       
                        // Delete related records in the 'train' table
                        $stmt2 = $pdo->prepare("DELETE FROM train WHERE activity_id = :del_id");
                        $stmt2->execute(['del_id' => $del_id]);

                        // Delete the activity from the 'activity' table
                        $stmt3 = $pdo->prepare("DELETE FROM activity WHERE activity_id = :del_id");
                        $stmt3->execute(['del_id' => $del_id]);
                       
                        // Commit the transaction
                        $pdo->commit();

                    } catch (PDOException $e) {
                         // Rollback the transaction in case of errors
                         $pdo->rollBack();
                         throw $e;
                    }
                  
                } else {
                    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                    echo "<script type='text/javascript'>alert('ไม่สามารถลบโครงการได้ เนื่องจากยืนยันจัดตั้งกลุ่มแล้ว');</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            echo "<script type='text/javascript'>alert('เกิดข้อผิดพลาดในการลบโครงการ: " . $e->getMessage() . "');</script>";
        }
    }
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=act_main.php'>";
}
?>