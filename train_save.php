<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit();
}

require_once "./includes/connect.php";

// Helper: redirect กลับพร้อมแสดง alert
function redirect_with_alert($message, $url, $delay = 0) {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
    if (!empty($message)) {
        echo "<script type='text/javascript'>alert('" . addslashes($message) . "');</script>";
    }
    echo "<meta http-equiv=\"refresh\" content=\"{$delay};URL={$url}\" />";
    exit();
}

try {
    // ตรวจสอบว่ามีการส่งค่า act_id มาหรือไม่
    if (!isset($_REQUEST['act_id'])) {
        redirect_with_alert('ไม่พบรหัสกิจกรรม', 'act_main.php');
    }

    $act_id = filter_var($_REQUEST['act_id'], FILTER_VALIDATE_INT);
    if ($act_id === false) {
        redirect_with_alert('รหัสกิจกรรมไม่ถูกต้อง', 'act_main.php');
    }

    $school_id = $_SESSION['ss_school_id'];
    $return_url = "train_in.php?act_id=" . $act_id;

    // ตรวจสอบกิจกรรมว่าเป็นของโรงเรียนปัจจุบัน
    $stmt = $pdo->prepare("SELECT activity_id FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1");
    $stmt->execute([
        'act_id' => $act_id,
        'school_id' => $school_id
    ]);

    if ($stmt->rowCount() == 0) {
        redirect_with_alert('ไม่พบข้อมูลกิจกรรม หรือไม่มีสิทธิ์เข้าถึง', 'act_main.php');
    }

    // รายชื่อนักเรียนที่ถูกเลือก (ถ้าไม่ได้เลือกใครเลย ให้เป็น array ว่าง = ลบทั้งหมด)
    $student_id_select = (isset($_REQUEST['student_id_select']) && is_array($_REQUEST['student_id_select']))
        ? $_REQUEST['student_id_select']
        : [];

    // ฟังก์ชันช่วย clamp คะแนนให้อยู่ในช่วก 0-127 (tinyint signed)
    function clamp_score($value) {
        $num = filter_var($value, FILTER_VALIDATE_INT);
        if ($num === false) {
            $num = 0;
        }
        return max(0, min(127, $num));
    }

    // ดึงข้อมูล train เดิมของกิจกรรมนี้ไว้ก่อน เพื่อเก็บคะแนนเดิม (ไม่พึ่ง hidden fields)
    $old_train = [];
    $old_stmt = $pdo->prepare("SELECT student_id, a_score, b_score, c_score, total_scores, status_assess 
                               FROM train WHERE activity_id = :act_id");
    $old_stmt->execute(['act_id' => $act_id]);
    while ($r = $old_stmt->fetch()) {
        $old_train[$r['student_id']] = $r;
    }

    // ดึงรายชื่อนักเรียนที่เป็นของโรงเรียนนี้ (กรองความปลอดภัย)
    $valid_students = [];
    if (!empty($student_id_select)) {
        $placeholders = implode(',', array_fill(0, count($student_id_select), '?'));
        $valid_stmt = $pdo->prepare("SELECT student_id FROM student 
                                     WHERE school_id = ? AND student_id IN ({$placeholders})");
        $params = array_merge([$school_id], $student_id_select);
        $valid_stmt->execute($params);
        $valid_students = $valid_stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // เริ่ม transaction
    $pdo->beginTransaction();

    // ลบข้อมูลเดิม
    $stmt = $pdo->prepare("DELETE FROM train WHERE activity_id = :act_id");
    $stmt->execute(['act_id' => $act_id]);

    // เตรียม prepared statement สำหรับการ insert
    $insert_stmt = $pdo->prepare("
        INSERT INTO train (
            activity_id, student_id, 
            a_score, b_score, c_score, 
            total_scores, status_assess
        ) VALUES (
            :activity_id, :student_id, 
            :a_score, :b_score, :c_score, 
            :total_scores, :status_assess
        )
    ");

    $inserted = 0;
    foreach ($valid_students as $sid) {
        $sid = (int)$sid;
        if ($sid <= 0) {
            continue;
        }

        // ถ้ามีข้อมูลเดิม ใช้คะแนนเดิม ถ้าไม่มีใช้ค่า default 0
        $old = $old_train[$sid] ?? null;

        $a = clamp_score($old['a_score'] ?? 0);
        $b = clamp_score($old['b_score'] ?? 0);
        $c = clamp_score($old['c_score'] ?? 0);

        $total_raw = $old['total_scores'] ?? '';
        if ($total_raw === '' || $total_raw === null) {
            $total = min(127, $a + $b + $c);
        } else {
            $total = clamp_score($total_raw);
        }

        $status = filter_var($old['status_assess'] ?? 0, FILTER_VALIDATE_INT);
        if ($status === false) {
            $status = 0;
        }
        $status = ($status == 1) ? '1' : '0';

        $insert_stmt->execute([
            'activity_id' => (int)$act_id,
            'student_id'  => $sid,
            'a_score'     => $a,
            'b_score'     => $b,
            'c_score'     => $c,
            'total_scores'=> $total,
            'status_assess'=> $status
        ]);
        $inserted++;
    }

    // อัพเดทข้อมูลสรุป
    $summary_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_study,
            SUM(CASE WHEN sex_id = '1' THEN 1 ELSE 0 END) as men_study,
            SUM(CASE WHEN sex_id = '2' THEN 1 ELSE 0 END) as women_study,
            SUM(CASE WHEN status_assess = '1' THEN 1 ELSE 0 END) as total_graduate
        FROM train 
        INNER JOIN student ON train.student_id = student.student_id 
        WHERE train.activity_id = :act_id AND student.school_id = :school_id
    ");

    $summary_stmt->execute([
        'act_id' => $act_id,
        'school_id' => $school_id
    ]);
    $summary = $summary_stmt->fetch();

    // อัพเดทตาราง activity
    $update_stmt = $pdo->prepare("
        UPDATE activity 
        SET total_study = :total_study,
            men_study = :men_study,
            women_study = :women_study,
            total_graduate = :total_graduate
        WHERE activity_id = :act_id
    ");

    $update_stmt->execute([
        'total_study' => (int)($summary['total_study'] ?? 0),
        'men_study' => (int)($summary['men_study'] ?? 0),
        'women_study' => (int)($summary['women_study'] ?? 0),
        'total_graduate' => (int)($summary['total_graduate'] ?? 0),
        'act_id' => $act_id
    ]);

    // ยืนยัน transaction
    $pdo->commit();

    // Redirect กลับไปหน้าแสดงผล
    header("Location: train_show.php?act_id=" . $act_id);
    exit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database Error in train_save.php: " . $e->getMessage());
    $act_id = isset($act_id) ? $act_id : '';
    $return_url = ($act_id !== '') ? "train_in.php?act_id=" . $act_id : "act_main.php";
    redirect_with_alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(), $return_url);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in train_save.php: " . $e->getMessage());
    $act_id = isset($act_id) ? $act_id : '';
    $return_url = ($act_id !== '') ? "train_in.php?act_id=" . $act_id : "act_main.php";
    redirect_with_alert('เกิดข้อผิดพลาด: ' . $e->getMessage(), $return_url);
}
?>
