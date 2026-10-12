<?php
// get_thai_address.php
// อ่านข้อมูลจาก ThepExcel-Thailand-Tambon.xlsx โดยตรง

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

require_once __DIR__ . '/vendor/autoload.php'; // ต้องติดตั้ง phpspreadsheet ก่อน

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelFile = __DIR__ . '/data/ThepExcel-Thailand-Tambon.xlsx';

if (!file_exists($excelFile)) {
    echo json_encode(['error' => 'ไม่พบไฟล์ Excel ที่ data/ThepExcel-Thailand-Tambon.xlsx']);
    exit;
}

try {
    $action = $_GET['action'] ?? 'provinces';
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    // ข้าม header แถวแรก
    $data = [];
    $header = array_shift($rows);

    // ปรับชื่อคอลัมน์ให้ตรงกับไฟล์จริง (จาก ThepExcel)
    // โดยทั่วไปจะมี: ProvinceThai, AmphureThai, TambonThai, ZipCode
    $colProvince = 'ProvinceThai';
    $colAmphure  = 'AmphureThai';
    $colTambon   = 'TambonThai';
    $colZip      = 'ZipCode';

    if ($action === 'provinces') {
        $provinces = [];
        foreach ($rows as $row) {
            if (!empty($row[$colProvince])) {
                $provinces[$row[$colProvince]] = true;
            }
        }
        $result = array_keys($provinces);
        sort($result);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    } elseif ($action === 'amphures') {
        $province = $_GET['province'] ?? '';
        $amphures = [];
        foreach ($rows as $row) {
            if ($row[$colProvince] === $province && !empty($row[$colAmphure])) {
                $amphures[$row[$colAmphure]] = true;
            }
        }
        $result = array_keys($amphures);
        sort($result);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    } elseif ($action === 'tambons') {
        $amphure = $_GET['amphure'] ?? '';
        $tambons = [];
        foreach ($rows as $row) {
            if ($row[$colAmphure] === $amphure && !empty($row[$colTambon])) {
                $tambons[] = [
                    'name' => $row[$colTambon],
                    'zip'  => $row[$colZip] ?? ''
                ];
            }
        }
        // เรียงตามชื่อ
        usort($tambons, fn($a, $b) => strcmp($a['name'], $b['name']));
        echo json_encode($tambons, JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'อ่านไฟล์ Excel ไม่ได้: ' . $e->getMessage()]);
}
?>
