<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo '<meta http-equiv="refresh" content="2;URL=index.php" />';
    exit;
}

include "./modules/head.inc.php";
include "./includes/connect.php";
include "./includes/function.php";

$edit_id = isset($_REQUEST['edit_id']) && is_numeric($_REQUEST['edit_id']) ? $_REQUEST['edit_id'] : null;

$payee_id = "";
$payee_name = "";
$taxid = "";
$payee_address = "";
$tel = "";

if ($edit_id !== null) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM payee WHERE payee_id = :edit_id AND school_id = :school_id LIMIT 1");
        $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
        $stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            $payee_id      = $row['payee_id'];
            $payee_name    = $row['payee_name'];
            $taxid         = $row['taxid'];
            $payee_address = $row['payee_address'];
            $tel           = $row['tel'];
        } else {
            echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error fetching payee data: " . $e->getMessage();
        error_log("PDO Exception: " . $e->getMessage());
        exit;
    }
}

// ===== ตัวแปรช่วย =====
$isEdit       = !empty($payee_id);
$pageTitle    = $isEdit ? 'แก้ไขข้อมูลผู้ขาย/ร้านค้า' : 'เพิ่มผู้ขาย/ร้านค้าใหม่';
$modeBadge    = $isEdit ? 'โหมดแก้ไขข้อมูล' : 'โหมดเพิ่มข้อมูลใหม่';
$modeIcon     = $isEdit ? 'edit' : 'plus-circle';
$badgeIcon    = $isEdit ? 'pen-to-square' : 'star';
$formAction   = $isEdit ? 'payee_save.php?submit=EDIT' : 'payee_save.php';
$backUrl      = 'payee_main.php';
$mainUrl      = 'payee_main.php';
$homeUrl      = 'index.php';
$fontUrl      = 'https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap';
$cleaveUrl    = 'https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <title><?= $pageTitle ?> | C-Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="<?= $fontUrl ?>" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(15,23,42,0.08);
            --shadow-lg: 0 10px 40px rgba(79,70,229,0.15);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #f5f7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }

        .modern-breadcrumb {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }
        .modern-breadcrumb .breadcrumb { margin: 0; }
        .modern-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        .payee-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .payee-hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .payee-hero::after {
            content: '';
            position: absolute;
            bottom: -50%; left: -10%;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            position: relative; z-index: 2;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .hero-avatar {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            border: 2px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .hero-title {
            font-size: 1.6rem; font-weight: 700;
            margin: 0; letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.95rem; opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        .mode-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-hero-back {
            background: white;
            color: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-hero-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }

        .modern-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
        }
        .modern-card:hover { box-shadow: var(--shadow-md); }
        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-soft);
            padding: 1.1rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .modern-card .card-header h5 {
            margin: 0;
            font-size: 1.05rem; font-weight: 700;
            color: var(--text-dark);
            display: flex; align-items: center; gap: 0.6rem;
        }
        .card-header-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .modern-card .card-body { padding: 1.75rem; }

        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed var(--border-soft);
        }
        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .form-section-title {
            font-size: 0.95rem; font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex; align-items: center; gap: 0.6rem;
        }
        .form-section-title::before {
            content: '';
            width: 4px; height: 18px;
            background: linear-gradient(180deg, #4f46e5, #ec4899);
            border-radius: 4px;
        }
        .form-section-title i { color: var(--primary); font-size: 0.95rem; }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .form-label i { color: var(--primary); }

        .form-control {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-soft);
            padding: 0.6rem 0.9rem;
            font-size: 0.925rem;
            background: white;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            background: white;
        }
        .form-control::placeholder { color: #cbd5e1; font-weight: 400; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        .form-actions {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .btn-modern-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            color: white;
            filter: brightness(1.05);
        }
        .btn-modern-secondary {
            background: white;
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
            padding: 0.7rem 2rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none;
        }
        .btn-modern-secondary:hover {
            background: var(--bg-soft);
            color: var(--text-dark);
            transform: translateY(-2px);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modern-card { animation: fadeInUp 0.4s ease-out backwards; animation-delay: 0.1s; }

        @media (max-width: 768px) {
            .payee-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.2rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .modern-card .card-body { padding: 1.15rem; }
            .btn-modern-primary, .btn-modern-secondary {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= $homeUrl ?>"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="<?= $mainUrl ?>">ข้อมูลผู้ขาย/ร้านค้า</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= $isEdit ? 'แก้ไขข้อมูล' : 'เพิ่มข้อมูลใหม่' ?></li>
                </ol>
            </nav>

            <div class="payee-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-<?= $modeIcon ?>"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-<?= $badgeIcon ?>"></i> <?= $modeBadge ?>
                            </span>
                            <h1 class="hero-title mt-2"><?= $pageTitle ?></h1>
                            <p class="hero-subtitle">กรอกข้อมูลผู้ขาย/ร้านค้าให้ครบถ้วนเพื่อบันทึกในระบบ C-Smart</p>
                        </div>
                    </div>
                    <a href="<?= $backUrl ?>" class="btn-hero-back">
                        <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                    </a>
                </div>
            </div>

            <div class="modern-card">
                <div class="card-header">
                    <h5>
                        <span class="card-header-icon"><i class="fas fa-store"></i></span>
                        <?= $isEdit ? 'แก้ไขข้อมูลผู้ขาย/ร้านค้า' : 'ข้อมูลผู้ขาย/ร้านค้าใหม่' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form class="form" action="<?= $formAction ?>" method="post" name="form1" id="form1" novalidate>

                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-id-card"></i> ข้อมูลทางภาษีและชื่อร้าน</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="taxid" class="form-label">
                                        <i class="fas fa-id-card"></i> เลขประจำตัวผู้เสียภาษี / เลขบัตรประชาชน
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="taxid"
                                           name="taxid"
                                           autocomplete="off"
                                           required
                                           value="<?= htmlspecialchars($taxid) ?>"
                                           placeholder="เช่น 1-2345-67890-12-3">
                                    <div class="invalid-feedback">กรุณากรอกเลขประจำตัวผู้เสียภาษีให้ถูกต้อง (13 หลัก)</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="payee_name" class="form-label">
                                        <i class="fas fa-store-alt"></i> ชื่อผู้ขาย/ร้านค้า
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="payee_name"
                                           name="payee_name"
                                           autocomplete="off"
                                           required
                                           placeholder="เช่น ร้านจันทบุรีค้าปลีก โดยนายประหยัด อดออม"
                                           value="<?= htmlspecialchars($payee_name) ?>">
                                    <div class="invalid-feedback">กรุณากรอกชื่อผู้ขาย/ร้านค้า</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> ข้อมูลที่อยู่และการติดต่อ</div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="payee_address" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> ที่อยู่
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="payee_address"
                                              name="payee_address"
                                              rows="2"
                                              required
                                              placeholder="เช่น 46 หมู่ 6 ต.พลับพลา อ.เมืองจันทบุรี จ.จันทบุรี 22000"><?= htmlspecialchars($payee_address) ?></textarea>
                                    <div class="invalid-feedback">กรุณากรอกที่อยู่</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="tel" class="form-label">
                                        <i class="fas fa-phone"></i> โทรศัพท์
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="tel"
                                           name="tel"
                                           autocomplete="off"
                                           required
                                           value="<?= htmlspecialchars($tel) ?>"
                                           placeholder="เช่น 039-123456">
                                    <div class="invalid-feedback">กรุณากรอกเบอร์โทรศัพท์</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions d-flex flex-column flex-md-row gap-3 justify-content-center">
                            <button type="submit" class="btn-modern-primary" onclick="CheckPID()">
                                <i class="fas fa-save"></i> บันทึกข้อมูล
                            </button>
                            <a href="<?= $backUrl ?>" class="btn-modern-secondary">
                                <i class="fas fa-times"></i> ยกเลิก
                            </a>
                            <input type="hidden" id="payee_id" name="payee_id" value="<?= htmlspecialchars($payee_id) ?>">
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script src="<?= $cleaveUrl ?>"></script>

<script>
    $(function () {
        if (document.getElementById('taxid')) {
            new Cleave('#taxid', {
                numericOnly: true,
                delimiter: '-',
                blocks: [1, 4, 5, 2, 1]
            });
        }

        if (document.getElementById('tel')) {
            new Cleave('#tel', {
                numericOnly: true,
                delimiter: '-',
                blocks: [3, 7]
            });
        }

        $('#taxid').on('input', function(e) {
            if (e.target.checkValidity() == false) {
                e.target.setCustomValidity("");
            }
        });
    });

    function CheckPID() {
        var taxid = document.getElementById("taxid");
        if (!taxid) return;

        var id = taxid.value.replace(/(_)|(-)/g, '');

        if (id.length == 13) {
            for (var i = 0, sum = 0; i < 12; i++) {
                sum += parseFloat(id.charAt(i)) * (13 - i);
            }
            if ((11 - sum % 11) % 10 != parseFloat(id.charAt(12))) {
                taxid.setCustomValidity("เลขประจำตัวผู้เสียภาษีไม่ถูกต้อง");
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    url: "./ajax/payee_taxid_check",
                    data: {
                        payee_id: $('#payee_id').val(),
                        taxid: $('#taxid').val()
                    },
                    async: false,
                    success: function(result) {
                        if (result == "false") {
                            taxid.setCustomValidity("เลขประจำตัวผู้เสียภาษีซ้ำ");
                            return false;
                        }
                    }
                });
            }
        } else {
            taxid.setCustomValidity("เลขประจำตัวผู้เสียภาษีไม่ครบ 13 หลัก");
            return false;
        }
    }
</script>
</body>
</html>