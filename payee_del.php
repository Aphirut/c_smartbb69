<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";

    $del_id = isset($_REQUEST['del_id']) ? $_REQUEST['del_id'] : null;

    if ($del_id !== null && is_numeric($del_id)) {
        try {
            // Check if the payee is associated with any procurements
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM procurement WHERE payee_id = :del_id");
            $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // Delete the payee if no associated procurements
                $stmt = $pdo->prepare("DELETE FROM payee WHERE payee_id = :del_id AND school_id = :school_id");
                $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
                $stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
                $stmt->execute();
            } else {
                echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
                echo "<script type='text/javascript'>alert('ไม่สามารถลบผู้ขาย/ร้านค้าได้ เนื่องจากจัดซื้อจัดจ้างแล้ว');</script>";
            }

        } catch (PDOException $e) {
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            echo "เกิดข้อผิดพลาดในการดำเนินการ: " . $e->getMessage();
            error_log("PDO Exception: " . $e->getMessage());
            exit; // Important to exit to prevent further errors
        }
    }
    else
    {
        // $del_id was not a number or was null
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
            echo "<script type='text/javascript'>alert('ค่า del_id ไม่ถูกต้อง');</script>";
    }

    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=payee_main.php'>";
}
?>