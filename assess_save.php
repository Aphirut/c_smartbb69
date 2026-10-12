<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit; // เพิ่ม exit เพื่อไม่ให้รันโค้ดส่วนที่เหลือ
} else {
    include "./includes/connect.php";
    $act_id = isset($_REQUEST['act_id']) ? $_REQUEST['act_id'] : '';

    if ($act_id != "" && is_numeric($act_id)) {
        try {
            // ตรวจสอบผู้เรียนว่าเป็นของสถานศึกษานั้น ๆ หรือไม่
            $sql = "SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id AND status_line != '' LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
            
            if ($stmt->rowCount() > 0) {
                $student_id  = isset($_REQUEST['student_id']) ? $_REQUEST['student_id'] : [];
                $a_score = isset($_REQUEST['a_score']) ? $_REQUEST['a_score'] : [];
                $b_score = isset($_REQUEST['b_score']) ? $_REQUEST['b_score'] : [];
                $c_score = isset($_REQUEST['c_score']) ? $_REQUEST['c_score'] : [];
                $total_scores = isset($_REQUEST['total_scores']) ? $_REQUEST['total_scores'] : [];

                $total_graduate = 0;
                if (count($total_scores) > 0) {
                    foreach ($total_scores as $i => $value) {
                        $pass = ($total_scores[$i] > 59 && $a_score[$i] >= 12) ? '1' : '0';
                        if ($pass === '1') {
                            $total_graduate++;
                        }
                        
                        $sql = "UPDATE train SET 
                                a_score=:a_score, b_score=:b_score, c_score=:c_score, 
                                total_scores=:total_scores, status_assess=:status_assess 
                                WHERE student_id = :student_id AND activity_id = :act_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            'a_score' => $a_score[$i],
                            'b_score' => $b_score[$i],
                            'c_score' => $c_score[$i],
                            'total_scores' => $total_scores[$i],
                            'status_assess' => $pass,
                            'student_id' => $student_id[$i],
                            'act_id' => $act_id
                        ]);
                    }

                    $sql = "UPDATE activity SET 
                            status_assess='1', total_graduate=:total_graduate 
                            WHERE activity_id = :act_id AND school_id = :school_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['total_graduate' => $total_graduate, 'act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Handle the error, maybe log it or display a message
            exit;
        }
    }
    echo "<meta http-equiv='refresh' content='0;url=assess_show.php?act_id=$act_id'>";
}
?>