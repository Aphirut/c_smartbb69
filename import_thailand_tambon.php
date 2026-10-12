<?php
/**
 * import_thailand_tambon.php
 * นำเข้าข้อมูลจังหวัด/อำเภอ/ตำบล/รหัสไปรษณีย์ จากไฟล์ data/ThepExcel-Thailand-Tambon.xlsx
 * เข้าตาราง thailand_tambon (อ่าน .xlsx โดยตรง ไม่ต้องใช้ PhpSpreadsheet)
 *
 * วิธีใช้ (command line):  php import_thailand_tambon.php
 */

$isCli = (PHP_SAPI === 'cli');
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}

set_time_limit(0);
require_once __DIR__ . '/includes/connect.php';

$excelFile = __DIR__ . '/data/ThepExcel-Thailand-Tambon.xlsx';
$sheetFile = 'xl/worksheets/sheet2.xml'; // ชีต TambonDatabase

if (!file_exists($excelFile)) {
    exit("ไม่พบไฟล์ {$excelFile}\n");
}

$zip = new ZipArchive();
if ($zip->open($excelFile) !== true) {
    exit("เปิดไฟล์ Excel ไม่ได้\n");
}

/* ---------- 1) อ่าน sharedStrings ---------- */
$shared = [];
$ssXml = $zip->getFromName('xl/sharedStrings.xml');
if ($ssXml !== false) {
    $sx = new SimpleXMLElement($ssXml);
    foreach ($sx->si as $si) {
        if (isset($si->t)) {
            $shared[] = (string)$si->t;
        } else {
            $txt = '';
            foreach ($si->r as $r) {
                $txt .= (string)$r->t;
            }
            $shared[] = $txt;
        }
    }
    unset($sx, $ssXml);
}

function colIndex(string $ref): int
{
    preg_match('/^([A-Z]+)/', $ref, $m);
    $n = 0;
    foreach (str_split($m[1]) as $ch) {
        $n = $n * 26 + (ord($ch) - 64);
    }
    return $n;
}

/* ---------- 2) อ่านแถวจากชีต ---------- */
function readSheetRows(string $xml, array $shared, callable $onRow): void
{
    $reader = new XMLReader();
    $reader->XML($xml);
    $rowBuffer = '';
    $inRow = false;

    while ($reader->read()) {
        if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'row') {
            $rowBuffer = $reader->readOuterXML();
            $rowNode = new SimpleXMLElement($rowBuffer);

            $cells = [];
            foreach ($rowNode->c as $c) {
                $type = (string)$c['t'];
                $value = (string)$c->v;
                if ($type === 's') {
                    $value = $shared[(int)$value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string)$c->is->t;
                }
                $cells[colIndex((string)$c['r'])] = $value;
            }
            ksort($cells);
            $onRow($cells);
        }
    }
    $reader->close();
}

/* ---------- 3) สร้างตาราง ---------- */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `thailand_tambon` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `TambonID` INT UNSIGNED NOT NULL,
        `TambonThai` VARCHAR(150) NOT NULL,
        `DistrictID` INT UNSIGNED NOT NULL,
        `DistrictThai` VARCHAR(150) NOT NULL,
        `ProvinceID` INT UNSIGNED NOT NULL,
        `ProvinceThai` VARCHAR(150) NOT NULL,
        `ZipCode` VARCHAR(10) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_tambon_id` (`TambonID`),
        KEY `idx_province` (`ProvinceID`),
        KEY `idx_district` (`DistrictID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$pdo->exec("TRUNCATE TABLE `thailand_tambon`");

/* ---------- 4) นำเข้าข้อมูล ---------- */
$sheetXml = $zip->getFromName($sheetFile);
$zip->close();

if ($sheetXml === false) {
    exit("ไม่พบชีต {$sheetFile}\n");
}

$header = null;
$batch = [];
$total = 0;

$insert = $pdo->prepare("
    INSERT INTO `thailand_tambon`
        (`TambonID`, `TambonThai`, `DistrictID`, `DistrictThai`, `ProvinceID`, `ProvinceThai`, `ZipCode`)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$pdo->beginTransaction();

readSheetRows($sheetXml, $shared, function (array $cells) use (&$header, &$batch, &$total, $insert, $pdo) {
    if ($header === null) {
        $header = $cells; // แถวหัวตาราง
        echo "หัวตาราง: " . implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($header), $header)) . "\n";
        return;
    }

    $col = function (string $name) use ($header, $cells) {
        $idx = array_search($name, $header, true);
        return $idx === false ? '' : ($cells[$idx] ?? '');
    };

    $tambonId = trim($col('TambonID'));
    $tambonTh = trim($col('TambonThai'));
    $districtId = trim($col('DistrictID'));
    $districtTh = trim($col('DistrictThai'));
    $provinceId = trim($col('ProvinceID'));
    $provinceTh = trim($col('ProvinceThai'));
    $zip = trim($col('PostCodeMain'));

    if ($tambonId === '' || $tambonTh === '' || $districtId === '' || $provinceId === '') {
        return;
    }

    $batch[] = [(int)$tambonId, $tambonTh, (int)$districtId, $districtTh, (int)$provinceId, $provinceTh, $zip];

    if (count($batch) >= 500) {
        foreach ($batch as $row) {
            $insert->execute($row);
        }
        $total += count($batch);
        $batch = [];
        echo "นำเข้าแล้ว {$total} แถว\n";
    }
});

foreach ($batch as $row) {
    $insert->execute($row);
}
$total += count($batch);

$pdo->commit();

echo "เสร็จสิ้น: นำเข้าทั้งหมด {$total} แถว\n";
