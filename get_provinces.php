<?php
// get_provinces.php - สำหรับ dropdown จังหวัด (รองรับข้อมูลจาก Excel ThepExcel)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/connect.php';

// === ตั้งค่าตามตารางที่คุณ import จาก Excel ===
$table_name   = 'thailand_tambon';      // ชื่อตารางที่ import (เปลี่ยนตามจริง)
$col_province = 'ProvinceThai';         // คอลัมน์ชื่อจังหวัด

try {
    $sql = "SELECT DISTINCT `$col_province` AS name_th 
            FROM `$table_name` 
            WHERE `$col_province` IS NOT NULL AND `$col_province` != ''
            ORDER BY `$col_province` ASC";
    
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => 'ไม่สามารถโหลดข้อมูลจังหวัดได้: ' . $e->getMessage()]);
}
?>
