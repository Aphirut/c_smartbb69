<?php
// get_tambons.php - สำหรับ dropdown ตำบล (รองรับข้อมูลจาก Excel ThepExcel)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/connect.php';

// === ตั้งค่าตามตารางที่คุณ import จาก Excel ThepExcel ===
$table_name   = 'thailand_tambon';
$col_district = 'AmphureThai';
$col_tambon   = 'TambonThai';
$col_zip      = 'ZipCode';

$district_name = isset($_GET['district_name']) ? $_GET['district_name'] : '';

if (empty($district_name)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT `$col_tambon` AS name_th, `$col_zip` AS zip_code 
        FROM `$table_name` 
        WHERE `$col_district` = ? 
        ORDER BY `$col_tambon` ASC
    ");
    $stmt->execute([$district_name]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => 'ไม่สามารถโหลดข้อมูลตำบลได้: ' . $e->getMessage()]);
}
?>
