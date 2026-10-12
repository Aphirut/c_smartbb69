<?php
session_name("C_Smart");
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['ss_school_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบใหม่']);
    exit();
}

include "./includes/connect.php";

$school_id = $_SESSION['ss_school_id'];
$act_id = isset($_GET['act_id']) ? (int)$_GET['act_id'] : 0;

try {
    // ข้อมูลโครงการ + ชื่อหน่วยงาน (จำกัดเฉพาะโรงเรียนที่ล็อกอินอยู่)
    $stmt = $pdo->prepare("SELECT a.activity_id, a.activity_name,
                                  a.men_study, a.women_study,
                                  a.total_costs, a.food_cost, a.drink_cost,
                                  sc.school_name
                           FROM activity a
                           INNER JOIN school sc ON sc.school_id = a.school_id
                           WHERE a.activity_id = :act_id AND a.school_id = :school_id
                           LIMIT 1");
    $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activity) {
        http_response_code(404);
        echo json_encode(['error' => 'ไม่พบโครงการ']);
        exit();
    }

    // ผู้รับเงิน: ผู้รับเงินรายแรกที่ผูกกับโครงการนี้ในการจัดซื้อ/จัดจ้าง
    $stmt = $pdo->prepare("SELECT p.payee_name, p.payee_address, p.tel, p.taxid
                           FROM procurement pr
                           INNER JOIN payee p ON p.payee_id = pr.payee_id
                           WHERE pr.activity_id = :act_id
                           ORDER BY pr.procurement_id ASC
                           LIMIT 1");
    $stmt->execute(['act_id' => $act_id]);
    $payee = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    echo json_encode([
        'activity' => [
            'activity_id'   => (int)$activity['activity_id'],
            'activity_name' => $activity['activity_name'],
            'school_name'   => $activity['school_name'],
            'participants'  => (int)$activity['men_study'] + (int)$activity['women_study'],
            'total_costs'   => (float)$activity['total_costs'],
            'food_cost'     => (float)$activity['food_cost'],
            'drink_cost'    => (float)$activity['drink_cost'],
        ],
        'payee' => $payee,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("get_pt19_data.php Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล']);
}
