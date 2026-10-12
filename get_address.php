<?php
// get_address.php - dropdown จังหวัด/อำเภอ/ตำบล ทั่วประเทศไทย
// แหล่งข้อมูล: data/thailand_address.json (สร้างจาก ThepExcel-Thailand-Tambon.xlsx)
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = isset($_GET['action']) ? $_GET['action'] : 'provinces';

// ตัดคำนำหน้าออก เช่น "อำเภอบ้านบึง" -> "บ้านบึง", "เขตพระนคร" -> "พระนคร"
function cleanPlaceName($name, $prefixes)
{
    $name = trim((string)$name);
    foreach ($prefixes as $prefix) {
        if ($prefix !== '' && mb_substr($name, 0, mb_strlen($prefix)) === $prefix) {
            return trim(mb_substr($name, mb_strlen($prefix)));
        }
    }
    return $name;
}

$dataFile = __DIR__ . '/data/thailand_address.json';
$data = [];
if (is_file($dataFile)) {
    $decoded = json_decode((string)file_get_contents($dataFile), true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}

// fallback: ถ้าไม่มีไฟล์ข้อมูลกลาง ใช้ข้อมูลจากสถานศึกษา (ตาราง school)
if (empty($data)) {
    require_once __DIR__ . '/includes/connect.php';
    try {
        if ($action === 'provinces') {
            $stmt = $pdo->query("SELECT DISTINCT province AS name_th FROM school
                                 WHERE province IS NOT NULL AND province <> '' ORDER BY province ASC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'amphures') {
            $province = trim(isset($_GET['province']) ? $_GET['province'] : '');
            if ($province === '') { echo json_encode([]); exit; }
            $stmt = $pdo->prepare("SELECT DISTINCT amphur AS name_th FROM school
                                   WHERE province = ? AND amphur IS NOT NULL AND amphur <> '' ORDER BY amphur ASC");
            $stmt->execute([$province]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'tambons') {
            $province = trim(isset($_GET['province']) ? $_GET['province'] : '');
            $amphur   = trim(isset($_GET['amphur']) ? $_GET['amphur'] : '');
            if ($province === '' || $amphur === '') { echo json_encode([]); exit; }
            $stmt = $pdo->prepare("SELECT DISTINCT district AS name_th, postcode AS zip_code FROM school
                                   WHERE province = ? AND amphur = ? AND district IS NOT NULL AND district <> ''
                                   ORDER BY district ASC");
            $stmt->execute([$province, $amphur]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'ไม่สามารถโหลดข้อมูลที่อยู่ได้: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($action === 'provinces') {
    $out = [];
    foreach (array_keys($data) as $province) {
        $out[] = ['name_th' => $province];
    }
    echo json_encode($out, JSON_UNESCAPED_UNICODE);

} elseif ($action === 'amphures') {
    $province = trim(isset($_GET['province']) ? $_GET['province'] : '');
    $out = [];
    if ($province !== '' && isset($data[$province])) {
        foreach (array_keys($data[$province]) as $amphur) {
            $out[] = ['name_th' => cleanPlaceName($amphur, ['อำเภอ', 'เขต'])];
        }
    }
    echo json_encode($out, JSON_UNESCAPED_UNICODE);

} elseif ($action === 'tambons') {
    $province = trim(isset($_GET['province']) ? $_GET['province'] : '');
    $amphur   = trim(isset($_GET['amphur']) ? $_GET['amphur'] : '');
    $out = [];
    if ($province !== '' && isset($data[$province])) {
        // หา key อำเภอจริงในข้อมูลกลาง (อาจมีคำนำหน้า "อำเภอ/เขต" ต่างจากที่ส่งมา)
        $rawAmphur = null;
        foreach (array_keys($data[$province]) as $key) {
            if ($key === $amphur || cleanPlaceName($key, ['อำเภอ', 'เขต']) === $amphur) {
                $rawAmphur = $key;
                break;
            }
        }
        if ($rawAmphur !== null) {
            foreach ($data[$province][$rawAmphur] as $tambon) {
                $out[] = [
                    'name_th'  => cleanPlaceName(isset($tambon[0]) ? $tambon[0] : '', ['ตำบล', 'แขวง']),
                    'zip_code' => isset($tambon[1]) ? $tambon[1] : '',
                ];
            }
        }
    }
    echo json_encode($out, JSON_UNESCAPED_UNICODE);

} else {
    echo json_encode([]);
}
