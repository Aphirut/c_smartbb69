<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit();
}
include "./includes/connect.php";
include "./includes/function.php";
require_once "./includes/satisfaction_lib.php";
set_time_limit(600);

$act_id = (int)($_GET['act_id'] ?? 0);
$stmt = $pdo->prepare("SELECT a.activity_id, a.activity_name, a.format_id, c.course_name
                       FROM activity a LEFT JOIN course c ON a.course_id = c.course_id
                       WHERE a.activity_id = ? AND a.school_id = ?");
$stmt->execute([$act_id, $_SESSION['ss_school_id']]);
$act = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$act) { echo "ไม่พบข้อมูลกิจกรรม หรือไม่มีสิทธิ์เข้าถึง"; exit; }
sat_ensure_table($pdo);

// =========================
// รับข้อมูล
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgs = []; $errs = [];
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'sheet_file' && !empty($_FILES['sheet']['tmp_name'])) {
            $name = $_FILES['sheet']['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext === 'xlsx') $rows = sat_read_xlsx($_FILES['sheet']['tmp_name']);
            elseif ($ext === 'csv') $rows = sat_read_csv(file_get_contents($_FILES['sheet']['tmp_name']));
            else throw new RuntimeException('รองรับเฉพาะไฟล์ .xlsx หรือ .csv');
            [$resp, $ncols] = sat_parse_table($rows);
            $n = sat_insert($pdo, $act_id, 'ไฟล์: ' . $name, $resp);
            $msgs[] = "นำเข้า {$n} คน จาก {$name} (พบคอลัมน์คะแนน {$ncols} ข้อ)";
            if ($ncols < SAT_NUM_ITEMS) $errs[] = "ไฟล์มีคะแนนเพียง {$ncols} ข้อ (แบบ พต.12 มี " . SAT_NUM_ITEMS . " ข้อ) ระบบคำนวณจากข้อที่มี";
        } elseif ($action === 'gsheet' && trim($_POST['url'] ?? '') !== '') {
            [$resp, $ncols] = sat_parse_table(sat_gsheet_rows(trim($_POST['url'])));
            $n = sat_insert($pdo, $act_id, 'Google Sheet', $resp);
            $msgs[] = "นำเข้า {$n} คน จาก Google Sheet (พบคอลัมน์คะแนน {$ncols} ข้อ)";
            if ($ncols < SAT_NUM_ITEMS) $errs[] = "Google Sheet มีคะแนนเพียง {$ncols} ข้อ ระบบคำนวณจากข้อที่มี";
        } elseif ($action === 'scan' && !empty($_FILES['scans']['name'][0])) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            foreach ($_FILES['scans']['tmp_name'] as $i => $tmp) {
                $name = $_FILES['scans']['name'][$i];
                if (!$tmp || $_FILES['scans']['error'][$i] !== UPLOAD_ERR_OK) { $errs[] = "อัปโหลด {$name} ไม่สำเร็จ"; continue; }
                $mime = $finfo->file($tmp);
                if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true)) { $errs[] = "{$name}: รองรับเฉพาะ PDF, JPG, PNG, WEBP"; continue; }
                try {
                    $resp = sat_ai_extract($tmp, $mime, $name);
                    $n = sat_insert($pdo, $act_id, 'AI: ' . $name, $resp);
                    $msgs[] = "AI อ่าน {$name} ได้ {$n} คน";
                } catch (Throwable $e) {
                    $errs[] = "{$name}: " . $e->getMessage();
                }
            }
        } elseif ($action === 'update_all') {
            $up = $pdo->prepare("UPDATE satisfaction_response SET sex = ?, scores = ? WHERE id = ? AND activity_id = ?");
            $cnt = 0;
            foreach ((array)($_POST['rows'] ?? []) as $rid => $r) {
                $scores = [];
                for ($i = 0; $i < SAT_NUM_ITEMS; $i++) {
                    $v = (int)($r['s'][$i] ?? 0);
                    $scores[] = ($v >= 1 && $v <= 5) ? $v : null;
                }
                $sex = in_array($r['sex'] ?? '', ['ชาย', 'หญิง'], true) ? $r['sex'] : '';
                $up->execute([$sex, json_encode($scores), (int)$rid, $act_id]);
                $cnt++;
            }
            $msgs[] = "บันทึกการแก้ไขทั้งหมดแล้ว ({$cnt} คน)";
        } elseif ($action === 'update_row') {
            $scores = [];
            for ($i = 0; $i < SAT_NUM_ITEMS; $i++) {
                $v = (int)($_POST['s'][$i] ?? 0);
                $scores[] = ($v >= 1 && $v <= 5) ? $v : null;
            }
            $sex = in_array($_POST['sex'] ?? '', ['ชาย', 'หญิง'], true) ? $_POST['sex'] : '';
            $pdo->prepare("UPDATE satisfaction_response SET sex = ?, scores = ? WHERE id = ? AND activity_id = ?")
                ->execute([$sex, json_encode($scores), (int)$_POST['id'], $act_id]);
            $msgs[] = 'บันทึกการแก้ไขแล้ว';
        } elseif ($action === 'delete_row') {
            $pdo->prepare("DELETE FROM satisfaction_response WHERE id = ? AND activity_id = ?")->execute([(int)$_POST['id'], $act_id]);
            $msgs[] = 'ลบผู้ตอบ 1 คนแล้ว';
        } elseif ($action === 'delete_source') {
            $pdo->prepare("DELETE FROM satisfaction_response WHERE activity_id = ? AND source = ?")->execute([$act_id, $_POST['source'] ?? '']);
            $msgs[] = 'ลบข้อมูลแล้ว';
        } elseif ($action === 'clear') {
            $pdo->prepare("DELETE FROM satisfaction_response WHERE activity_id = ?")->execute([$act_id]);
            $msgs[] = 'ล้างข้อมูลทั้งหมดแล้ว';
        } else {
            $errs[] = 'กรุณาเลือกไฟล์หรือใส่ลิงก์';
        }
    } catch (Throwable $e) {
        $errs[] = $e->getMessage();
    }
    $_SESSION['sat_flash'] = [$msgs, $errs];
    header("Location: satisfied_calc.php?act_id={$act_id}");
    exit;
}
[$flashMsgs, $flashErrs] = $_SESSION['sat_flash'] ?? [[], []];
unset($_SESSION['sat_flash']);

$rows = sat_load($pdo, $act_id);
$sum = sat_summary($rows);
$sources = [];
foreach ($rows as $r) $sources[$r['source']] = ($sources[$r['source']] ?? 0) + 1;

$title = ($act['format_id'] == 1 || !$act['course_name']) ? $act['activity_name'] : $act['course_name'];
$e = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$f2 = function ($n) { return number_format($n, 2); };
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>คำนวณความพึงพอใจ | C-Smart</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, .app-main { font-family: 'Sarabun', sans-serif; }
        .sc-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 14px rgba(0,0,0,.04); padding: 1.25rem; margin-bottom: 1rem; }
        .sc-hero { background: linear-gradient(135deg, #10b981, #059669); color: #fff; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; }
        .sc-hero h1 { font-size: 1.4rem; font-weight: 700; margin: 0; }
        .stat { border-radius: 12px; padding: .9rem 1rem; background: #f8fafc; height: 100%; }
        .stat .v { font-size: 1.6rem; font-weight: 700; color: #047857; line-height: 1.2; }
        .stat .l { font-size: .85rem; color: #64748b; }
        .up-box { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 1rem; height: 100%; }
        .up-box h6 { font-weight: 600; }
        table.sat th { background: #ecfdf5; color: #065f46; text-align: center; vertical-align: middle; }
        table.sat td.n { text-align: center; }
        table.sat tr.part td { background: #f1f5f9; font-weight: 600; }
        table.sat tr.total td { background: #d1fae5; font-weight: 700; }
        .pt13-box { background: #eef2ff; border-left: 4px solid #4f46e5; border-radius: 10px; padding: .9rem 1rem; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">
<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>
<main class="app-main">
<div class="app-content">
<div class="container-fluid py-3">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="paper_satisfied.php">ประเมินความพึงพอใจ</a></li>
            <li class="breadcrumb-item active">คำนวณความพึงพอใจ</li>
        </ol>
    </nav>

    <div class="sc-hero">
        <div class="small opacity-75"><i class="fas fa-calculator me-1"></i>คำนวณผลการประเมินความพึงพอใจ (แบบ พต.12)</div>
        <h1><?= $e($title) ?></h1>
        <div class="small opacity-75">รหัสโครงการ #<?= $act_id ?></div>
    </div>

    <?php foreach ($flashMsgs as $m): ?><div class="alert alert-success py-2"><i class="fas fa-check-circle me-1"></i><?= $e($m) ?></div><?php endforeach; ?>
    <?php foreach ($flashErrs as $m): ?><div class="alert alert-warning py-2"><i class="fas fa-exclamation-triangle me-1"></i><?= $e($m) ?></div><?php endforeach; ?>

    <!-- นำเข้าข้อมูล -->
    <div class="sc-card">
        <h6 class="fw-bold mb-3"><i class="fas fa-upload me-1 text-success"></i>นำเข้าผลแบบประเมิน</h6>
        <div class="row g-3">
            <div class="col-lg-4">
                <form class="up-box" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="sheet_file">
                    <h6><i class="fas fa-file-excel text-success me-1"></i>ไฟล์จาก Google Sheets</h6>
                    <p class="small text-muted mb-2">ดาวน์โหลดจาก Google Sheets เป็น .xlsx หรือ .csv (ไฟล์ &gt; ดาวน์โหลด) คำตอบเป็นตัวเลข 1-5 หรือคำว่า มากที่สุด…น้อยที่สุด ก็ได้</p>
                    <input type="file" name="sheet" accept=".xlsx,.csv" class="form-control form-control-sm mb-2" required>
                    <button class="btn btn-success btn-sm"><i class="fas fa-file-import me-1"></i>นำเข้า</button>
                </form>
            </div>
            <div class="col-lg-4">
                <form class="up-box" method="post">
                    <input type="hidden" name="action" value="gsheet">
                    <h6><i class="fab fa-google text-primary me-1"></i>ลิงก์ Google Sheets</h6>
                    <p class="small text-muted mb-2">วางลิงก์ชีตคำตอบจาก Google Forms (ต้องแชร์แบบ “ทุกคนที่มีลิงก์ดูได้”)</p>
                    <input type="url" name="url" class="form-control form-control-sm mb-2" placeholder="https://docs.google.com/spreadsheets/d/..." required>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-link me-1"></i>ดึงข้อมูล</button>
                </form>
            </div>
            <div class="col-lg-4">
                <form class="up-box" method="post" enctype="multipart/form-data" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>AI กำลังอ่าน...';">
                    <input type="hidden" name="action" value="scan">
                    <h6><i class="fas fa-robot text-warning me-1"></i>รูปภาพ / PDF (Typhoon OCR)</h6>
                    <p class="small text-muted mb-2">สแกนหรือถ่ายรูปแบบ พต.12 ที่กรอกแล้ว เลือกได้หลายไฟล์ (JPG, PNG, PDF) Typhoon OCR จะอ่านเครื่องหมาย ✓ ให้ (1 หน้า = 1 คน)</p>
                    <input type="file" name="scans[]" accept=".pdf,.jpg,.jpeg,.png,.webp" multiple class="form-control form-control-sm mb-2" required>
                    <button class="btn btn-warning btn-sm"><i class="fas fa-magic me-1"></i>ประมวลผลด้วย Typhoon</button>
                </form>
            </div>
        </div>

        <?php if ($sources): ?>
        <hr>
        <div class="small fw-semibold mb-2">ข้อมูลที่นำเข้าแล้ว</div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <?php foreach ($sources as $src => $cnt): ?>
            <form method="post" class="d-inline" onsubmit="return confirm('ลบข้อมูลชุดนี้?');">
                <input type="hidden" name="action" value="delete_source">
                <input type="hidden" name="source" value="<?= $e($src) ?>">
                <span class="badge bg-light text-dark border px-2 py-2"><?= $e($src) ?> — <?= $cnt ?> คน
                    <button class="btn btn-link btn-sm p-0 ms-1 text-danger" title="ลบ"><i class="fas fa-times"></i></button></span>
            </form>
            <?php endforeach; ?>
            <form method="post" class="ms-auto" onsubmit="return confirm('ล้างข้อมูลทั้งหมดของโครงการนี้?');">
                <input type="hidden" name="action" value="clear">
                <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>ล้างทั้งหมด</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($sum): $o = $sum['overall']; ?>
    <!-- สรุปภาพรวม -->
    <div class="sc-card">
        <div class="row g-3">
            <div class="col-6 col-lg-2"><div class="stat"><div class="v"><?= $sum['respondents'] ?></div><div class="l">ผู้ตอบ (ชาย <?= $sum['sex']['ชาย'] ?> / หญิง <?= $sum['sex']['หญิง'] ?>)</div></div></div>
            <div class="col-6 col-lg-2"><div class="stat"><div class="v"><?= $f2($o['mean']) ?></div><div class="l">ค่าเฉลี่ย (x̄) จาก 5</div></div></div>
            <div class="col-6 col-lg-2"><div class="stat"><div class="v"><?= $f2($o['sd']) ?></div><div class="l">ส่วนเบี่ยงเบนมาตรฐาน (S.D.)</div></div></div>
            <div class="col-6 col-lg-3"><div class="stat"><div class="v"><?= $f2($o['percent']) ?>%</div><div class="l">ร้อยละความพึงพอใจ</div></div></div>
            <div class="col-12 col-lg-3"><div class="stat"><div class="v"><?= $e($o['level']) ?></div><div class="l">ระดับความพึงพอใจ (ระดับคุณภาพ: <?= $e($o['quality']) ?>)</div></div></div>
        </div>
        <?php $use = sat_usage_counts($rows); $useAny = count(array_filter($rows, fn($r) => !empty($r['usage']))); ?>
        <div class="mt-3">
            <div class="small fw-semibold mb-2"><i class="fas fa-seedling me-1 text-success"></i>พต.13 ข้อ 11.3 ผู้ผ่านการฝึกอบรมได้นำความรู้ไปใช้จริง (ตอบได้มากกว่า 1 ข้อ)</div>
            <div class="row g-2">
                <?php foreach (sat_usage_labels() as $k => $lbl): ?>
                <div class="col-6 col-lg-3"><div class="stat"><div class="v"><?= $use[$k] ?> <span class="fs-6">คน</span></div><div class="l"><?= $e($lbl) ?></div></div></div>
                <?php endforeach; ?>
            </div>
            <div class="small text-muted mt-1">
                <?php if ($useAny): ?>มีผู้ตอบข้อนี้ <?= $useAny ?> จาก <?= $sum['respondents'] ?> คน (จาก Google Sheets/ไฟล์ตาราง)
                <?php else: ?>ยังไม่มีข้อมูลข้อนี้ — ได้เฉพาะจาก Google Sheets/ไฟล์ตารางที่มีคอลัมน์ "ผู้ผ่านการฝึกอบรมได้นำความรู้ไปใช้จริง" ถ้านำเข้าจากรูป/PDF ให้ใช้แบบ <a href="new_report/pt20.php?act_id=<?= $act_id ?>" target="_blank">พต.20</a> แล้วกรอกด้วยมือ<?php endif; ?>
            </div>
        </div>
        <div class="pt13-box mt-3">
            <i class="fas fa-file-alt me-1"></i>ข้อความที่จะใส่ใน <b>แบบ พต.13 ข้อ 11.2</b>:
            “อยู่ในระดับ <b><?= $e($o['quality']) ?> คิดเป็นร้อยละ <?= $f2($o['percent']) ?></b>”
            <a class="btn btn-sm btn-primary ms-2" href="new_report/pt13.php?act_id=<?= $act_id ?>" target="_blank"><i class="fas fa-print me-1"></i>เปิด พต.13</a>
        </div>
    </div>

    <!-- ตารางรายข้อ -->
    <div class="sc-card">
        <h6 class="fw-bold mb-3"><i class="fas fa-table me-1 text-success"></i>ผลการประเมินรายข้อ</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm sat mb-2">
                <thead><tr><th style="width:50px">ข้อ</th><th>รายการประเมิน</th><th style="width:60px">n</th><th style="width:80px">x̄</th><th style="width:80px">S.D.</th><th style="width:90px">ร้อยละ</th><th style="width:110px">ระดับ</th></tr></thead>
                <tbody>
                <?php foreach ($sum['parts'] as $p): ?>
                    <tr class="part"><td colspan="7"><?= $e($p['name']) ?></td></tr>
                    <?php foreach ($p['items'] as $it): ?>
                    <tr>
                        <td class="n"><?= $it['no'] ?></td><td><?= $e($it['text']) ?></td>
                        <?php if ($it['n']): ?>
                        <td class="n"><?= $it['n'] ?></td><td class="n"><?= $f2($it['mean']) ?></td><td class="n"><?= $f2($it['sd']) ?></td><td class="n"><?= $f2($it['percent']) ?></td><td class="n"><?= $e($it['level']) ?></td>
                        <?php else: ?><td colspan="5" class="n text-muted">ไม่มีข้อมูล</td><?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="part"><td></td><td class="text-end">เฉลี่ย<?= $e(preg_replace('/^ตอนที่ \d+ /u', '', $p['name'])) ?></td>
                        <td class="n"><?= $p['n'] ?></td><td class="n"><?= $f2($p['mean']) ?></td><td class="n"><?= $f2($p['sd']) ?></td><td class="n"><?= $f2($p['percent']) ?></td><td class="n"><?= $e($p['level']) ?></td></tr>
                <?php endforeach; ?>
                    <tr class="total"><td></td><td class="text-end">รวมทุกด้าน</td>
                        <td class="n"><?= $o['n'] ?></td><td class="n"><?= $f2($o['mean']) ?></td><td class="n"><?= $f2($o['sd']) ?></td><td class="n"><?= $f2($o['percent']) ?></td><td class="n"><?= $e($o['level']) ?></td></tr>
                </tbody>
            </table>
        </div>
        <div class="small text-muted">
            เกณฑ์: 4.51–5.00 มากที่สุด (ดีมาก) · 3.51–4.50 มาก (ดี) · 2.51–3.50 ปานกลาง · 1.51–2.50 น้อย (พอใช้) · 1.00–1.50 น้อยที่สุด (ควรปรับปรุง)<br>
            ร้อยละ = ค่าเฉลี่ย ÷ 5 × 100 · S.D. คำนวณแบบกลุ่มตัวอย่าง (n−1) · n ของแต่ละตอน/ภาพรวม = จำนวนคำตอบทั้งหมดในตอนนั้น
        </div>
    </div>
    <!-- ตรวจทาน/แก้ไขรายคน -->
    <div class="sc-card">
        <div class="d-flex align-items-center mb-2">
            <h6 class="fw-bold mb-0"><i class="fas fa-user-check me-1 text-success"></i>ตรวจทานคำตอบรายคน</h6>
            <button class="btn btn-success btn-sm ms-auto me-2" form="allForm"><i class="fas fa-save me-1"></i>บันทึกทั้งหมด</button>
            <form method="post" class="d-inline me-2" onsubmit="return confirm('ล้างข้อมูลแบบประเมินทั้งหมดของโครงการนี้ (<?= count($rows) ?> คน)? การลบกู้คืนไม่ได้');">
                <input type="hidden" name="action" value="clear">
                <button class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>ล้างข้อมูลทั้งหมด</button>
            </form><button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#rowsBox">แสดง/ซ่อน</button>
        </div>
        <p class="small text-muted mb-2">ผลจาก OCR อาจอ่านเครื่องหมายเลื่อนช่องได้ ควรเทียบกับแบบฟอร์มจริงแล้วแก้ไขให้ถูกต้อง (5 = มากที่สุด … 1 = น้อยที่สุด, ว่าง = ไม่ได้ตอบ)</p>
        <form id="allForm" method="post"><input type="hidden" name="action" value="update_all"></form>
        <div class="collapse show" id="rowsBox">
        <div class="table-responsive">
            <table class="table table-bordered table-sm sat mb-0" style="font-size:.85rem">
                <thead><tr><th>#</th><th>ที่มา</th><th>เพศ</th>
                    <?php for ($i = 1; $i <= SAT_NUM_ITEMS; $i++): ?><th><?= $i ?></th><?php endfor; ?><th></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $k => $r): $fid = 'r' . $r['id']; ?>
                    <tr>
                        <td class="n"><?= $k + 1 ?></td>
                        <td class="small text-muted" style="max-width:160px"><?= $e($r['source']) ?></td>
                        <td><select form="allForm" name="rows[<?= $r['id'] ?>][sex]" class="form-select form-select-sm" style="width:80px">
                            <?php foreach (['' => '-', 'ชาย' => 'ชาย', 'หญิง' => 'หญิง'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $r['sex'] === $v ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
                        </select></td>
                        <?php for ($i = 0; $i < SAT_NUM_ITEMS; $i++): $sv = $r['scores'][$i] ?? null; ?>
                        <td class="p-0"><input form="allForm" name="rows[<?= $r['id'] ?>][s][<?= $i ?>]" value="<?= $sv ?>" maxlength="1" pattern="[1-5]?" class="form-control form-control-sm text-center border-0 px-0 <?= $sv === null ? 'bg-warning-subtle' : '' ?>" style="width:34px"></td>
                        <?php endfor; ?>
                        <td class="text-nowrap">
                            
                            <form method="post" class="d-inline" onsubmit="return confirm('ลบผู้ตอบคนนี้?');">
                                <input type="hidden" name="action" value="delete_row"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-outline-danger btn-sm py-0" title="ลบ"><i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
    <?php else: ?>
    <div class="sc-card text-center text-muted py-5">
        <i class="fas fa-inbox fa-2x mb-2"></i><br>ยังไม่มีข้อมูลแบบประเมิน กรุณานำเข้าจากช่องด้านบน
    </div>
    <?php endif; ?>

</div>
</div>
</main>
<?php include "./modules/footer.inc.php"; ?>
</body>
</html>
