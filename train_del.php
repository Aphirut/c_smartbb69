<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
} else {
    include "./includes/connect.php";

    // แก้ไขการรับค่าจาก INPUT_REQUEST เป็น $_REQUEST
    $del_id = filter_input(INPUT_GET, 'del_id', FILTER_SANITIZE_NUMBER_INT) ?? 
              filter_input(INPUT_POST, 'del_id', FILTER_SANITIZE_NUMBER_INT);
    $act_id = filter_input(INPUT_GET, 'act_id', FILTER_SANITIZE_NUMBER_INT) ?? 
              filter_input(INPUT_POST, 'act_id', FILTER_SANITIZE_NUMBER_INT);

    if ($del_id && $act_id) {
        try {
            // เริ่ม Transaction
            $pdo->beginTransaction();

            // ตรวจสอบว่านักเรียนอยู่ในโรงเรียนปัจจุบันหรือไม่
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM student 
                                 WHERE student_id = :del_id 
                                 AND school_id = :school_id");
            $stmt->execute([
                'del_id' => $del_id,
                'school_id' => $_SESSION['ss_school_id']
            ]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // ลบข้อมูลการอบรม
                $stmt = $pdo->prepare("DELETE FROM train 
                                     WHERE student_id = :del_id 
                                     AND activity_id = :act_id");
                $stmt->execute([
                    'del_id' => $del_id,
                    'act_id' => $act_id
                ]);

                // อัพเดทข้อมูลสถิติในตาราง activity
                $sql = "SELECT 
                        COUNT(*) AS total_study,
                        SUM(CASE WHEN s.sex_id = '1' THEN 1 ELSE 0 END) AS men_study,
                        SUM(CASE WHEN s.sex_id = '2' THEN 1 ELSE 0 END) AS women_study,
                        SUM(CASE WHEN t.status_assess = '1' THEN 1 ELSE 0 END) AS total_graduate
                       FROM train t
                       INNER JOIN student s ON t.student_id = s.student_id
                       WHERE t.activity_id = :act_id 
                       AND s.school_id = :school_id";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'act_id' => $act_id,
                    'school_id' => $_SESSION['ss_school_id']
                ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // อัพเดทตาราง activity
                $stmt = $pdo->prepare("UPDATE activity 
                                     SET total_study = :total_study,
                                         men_study = :men_study,
                                         women_study = :women_study,
                                         total_graduate = :total_graduate
                                     WHERE activity_id = :act_id");
                $stmt->execute([
                    'total_study' => $row['total_study'] ?? 0,
                    'men_study' => $row['men_study'] ?? 0,
                    'women_study' => $row['women_study'] ?? 0,
                    'total_graduate' => $row['total_graduate'] ?? 0,
                    'act_id' => $act_id
                ]);

                // Commit Transaction
                $pdo->commit();
                
                echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
            } else {
                echo "<script>alert('ไม่พบข้อมูลนักเรียนในระบบ');</script>";
            }
        } catch (PDOException $e) {
            // Rollback หากมีข้อผิดพลาด
            $pdo->rollBack();
            error_log("Database Error: " . $e->getMessage());
            echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล');</script>";
        }
    }

    echo "<meta http-equiv='refresh' content='0;url=train_show.php?act_id=$act_id'>";
    exit;
}
?>
