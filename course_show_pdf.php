<?php
session_name("C_Smart");
session_start();

if (empty($_GET['file'])) {
    http_response_code(400);
    exit('ไม่พบไฟล์ที่ต้องการเปิด');
}

// ใช้ basename เพื่อป้องกัน path traversal (../../)
$fname = basename($_GET['file']);
$path  = __DIR__ . '/courses/' . $fname;

if (!is_file($path)) {
    http_response_code(404);
    exit('ไม่พบไฟล์ PDF');
}

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="document.pdf"; filename*=UTF-8\'\'' . rawurlencode($fname));
header('X-Content-Type-Options: nosniff');

readfile($path);
exit;
