<?php
// get_amphures.php - สำหรับ dropdown อำเภอ (รองรับข้อมูลจาก Excel ThepExcel)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/connect.php';

// === ตั้งค่าตามตารางที่คุณ import จาก Excel ThepExcel ===
$table_name   = 'thailand_tambon';   // ชื่อตารางที่ import (แก้ตามจริง)
$col_province = 'ProvinceThai';
$col_district = 'AmphureThai';

$province_name = isset($_GET['province_name']) ? $_GET['province_name'] : '';

if (empty($province_name)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT `$col_district` AS name_th 
        FROM `$table_name` 
        WHERE `$col_province` = ? 
        ORDER BY `$col_district` ASC
    ");
    $stmt->execute([$province_name]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => 'ไม่สามารถโหลดข้อมูลอำเภอได้: ' . $e->getMessage()]);
}
?>
