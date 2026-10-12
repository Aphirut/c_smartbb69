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

$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
$row = [];

if ($edit_id && is_numeric($edit_id)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :edit_id AND school_id = :school_id LIMIT 1");
        $stmt->execute(['edit_id' => $edit_id, 'school_id' => $_SESSION['ss_school_id']]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row['date_start'])) {
                 $row['date_start'] = ADYearToBEYear($row['date_start']);
            }
            if (!empty($row['date_end'])) {
                $row['date_end'] = ADYearToBEYear($row['date_end']);
            }
        } else {
            echo "<script>alert('ไม่พบข้อมูลกิจกรรม ID: " . $edit_id . "'); window.location='act_main.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>ข้อมูลกิจกรรม/โครงการ | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
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

        /* ===== Breadcrumb ===== */
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

        /* ===== Hero Header ===== */
        .activity-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .activity-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .activity-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .hero-avatar {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .hero-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }

        /* ===== Modern Cards ===== */
        .activity-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        .activity-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
        }
        .activity-card:hover { box-shadow: var(--shadow-md); }
        .activity-card .card-header {
            background: transparent;
            color: var(--text-dark);
            padding: 1.1rem 1.5rem 0.75rem 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .activity-card .card-header h3 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .card-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .card-header-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .card-header-icon.pink   { background: linear-gradient(135deg, #ec4899, #db2777); }
        .card-header-icon.orange { background: linear-gradient(135deg, #f97316, #ea580c); }
        .card-header-icon.green  { background: linear-gradient(135deg, #10b981, #059669); }
        .activity-card .card-body { padding: 1.5rem; }

        /* ===== Form Styles ===== */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }
        .input-group-text {
            background: var(--primary-light);
            border: 1px solid var(--border-soft);
            color: var(--primary);
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        }
        .input-group > .form-control,
        .input-group > .form-select {
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        }
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-soft);
            padding: 0.55rem 0.85rem;
            font-size: 0.925rem;
            background: white;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        }
        .form-control::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }

        /* ===== Section Title ===== */
        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed var(--border-soft);
        }
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .form-section-title::before {
            content: '';
            width: 4px;
            height: 18px;
            background: linear-gradient(180deg, #4f46e5, #ec4899);
            border-radius: 4px;
        }
        .form-section-title i { color: var(--primary); font-size: 0.95rem; }

        /* ===== Select2 styling ===== */
        .select2-container { width: 100% !important; }
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border-soft) !important;
            min-height: 42px !important;
            background-color: white !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.7;
            padding-left: 0.85rem;
            color: var(--text-dark);
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15) !important;
        }
        .input-group .select2-container { flex: 1 1 auto; }

        /* ===== Date mode toggle ===== */
        #date_start_end_show, #date_range_show {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 1rem;
            margin-top: 0.5rem;
        }

        /* ===== Cost Grid ===== */
        .cost-grid .input-group {
            box-shadow: var(--shadow-sm);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .cost-grid .input-group .form-control {
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        }
        .cost-grid .input-group .input-group-text:last-child {
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            font-weight: 600;
        }

        /* ===== Credit Toggle ===== */
        .cost-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
            gap: 0.5rem;
        }
        .credit-switch {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.15rem 0.55rem;
            background: var(--warning-light);
            border: 1px solid #fcd34d;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #92400e;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            margin: 0;
        }
        .credit-switch:hover { background: #fde68a; }
        .credit-switch input[type="checkbox"] { margin: 0; cursor: pointer; }
        .credit-switch i { font-size: 0.7rem; }

        .cost-input.is-credit {
            background: var(--warning-light) !important;
            color: #92400e !important;
            text-decoration: line-through;
            font-weight: 600;
        }

        .total-cost-box {
            background: linear-gradient(135deg, #eef2ff, #fdf2f8);
            border: 2px solid var(--primary);
            border-radius: var(--radius-sm);
            padding: 0.5rem;
        }
        .total-cost-box .input-group { box-shadow: none; }
        .total-cost-box .form-control {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary-dark);
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        }
        .total-cost-box .input-group-text {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        }
        .credit-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            padding: 0.4rem 0.75rem;
            background: var(--warning-light);
            border: 1px dashed #fcd34d;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            color: #92400e;
            font-weight: 600;
        }

        /* ===== Borrower Card ===== */
        .borrower-info-box {
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            border: 1px dashed #10b981;
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #065f46;
        }

        /* ===== Level Switch (พื้นฐาน / ชั้นกลาง / ชั้นสูง) ===== */
        .level-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.85rem;
        }
        .level-card {
            background: var(--bg-soft);
            border: 2px dashed var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 0.85rem 1rem;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .level-card:hover {
            border-color: var(--primary);
            background: #f1f5f9;
        }
        .level-card.active {
            background: var(--primary-light);
            border-color: var(--primary);
            border-style: solid;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.12);
        }
        .level-card.active:hover {
            background: var(--primary-light);
        }
        .level-card .form-check {
            margin-bottom: 0.5rem;
            padding-left: 1.85rem;
        }
        .level-card .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            margin-top: 0.15rem;
            cursor: pointer;
        }
        .level-card .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .level-card .form-check-label {
            font-weight: 700;
            color: var(--text-dark);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .level-card .level-badge {
            font-size: 0.7rem;
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
            background: white;
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-weight: 600;
        }
        .level-card.active .level-badge {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .level-card .input-group { box-shadow: var(--shadow-sm); }
        .level-card input[type="text"]:disabled {
            background: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
        }

        /* ===== Form Actions ===== */
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-modern-secondary:hover {
            background: var(--bg-soft);
            color: var(--text-dark);
            transform: translateY(-2px);
        }

        /* ===== Animation ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .activity-card { animation: fadeInUp 0.4s ease-out backwards; }
        .activity-card:nth-of-type(1) { animation-delay: 0.05s; }
        .activity-card:nth-of-type(2) { animation-delay: 0.1s; }
        .activity-card:nth-of-type(3) { animation-delay: 0.15s; }
        .activity-card:nth-of-type(4) { animation-delay: 0.2s; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .activity-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .activity-card .card-body { padding: 1.15rem; }
            .btn-modern-primary, .btn-modern-secondary { padding: 0.6rem 1.5rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="act_main.php">ข้อมูลกิจกรรม/โครงการ</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= $edit_id ? 'แก้ไขกลุ่ม/โครงการ' : 'เพิ่มกลุ่ม/โครงการ' ?></li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="activity-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-<?= $edit_id ? 'edit' : 'plus-circle' ?>"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-<?= $edit_id ? 'pen-to-square' : 'sparkles' ?>"></i>
                                <?= $edit_id ? 'โหมดแก้ไขข้อมูล' : 'โหมดเพิ่มข้อมูลใหม่' ?>
                            </span>
                            <h1 class="hero-title mt-2"><?= $edit_id ? 'แก้ไขกลุ่ม/โครงการ' : 'เพิ่มกลุ่ม/โครงการใหม่' ?></h1>
                            <p class="hero-subtitle">กรอกข้อมูลกิจกรรม/โครงการให้ครบถ้วนเพื่อบันทึกในระบบ C-Smart</p>
                        </div>
                    </div>
                    <a href="act_main.php" class="btn-hero-back">
                        <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                    </a>
                </div>
            </div>

            <form action="act_save.php<?php if ($edit_id) { echo "?submit=EDIT"; } ?>" method="post" id="activityForm">

                <!-- ===== Card 1: ข้อมูลหลักสูตร/โครงการ ===== -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <span class="card-header-icon"><i class="fas fa-book"></i></span>
                            ข้อมูลหลักสูตร/โครงการ
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label"><i class="fas fa-graduation-cap me-1 text-primary"></i> หลักสูตร/โครงการ</label>
                                <select class="form-select select2" id="course_id" name="course_id" required>
                                    <option value="" selected disabled>-- โปรดเลือก --</option>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM course ORDER BY course_name ASC, course_hour ASC");
                                        while ($course = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ($course['course_id'] == ($row['course_id'] ?? '')) ? "selected" : "";
                                            echo "<option value='{$course['course_id']}' $selected>{$course['course_name']} {$course['course_hour']} ชม.</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="format_id" class="form-label"><i class="fas fa-shapes me-1 text-primary"></i> รูปแบบการจัด</label>
                                <select class="form-select select2" id="format_id" name="format_id" required>
                                    <option value="1" selected>ฝึกอบรมประชาชน</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attribute_id" class="form-label"><i class="fas fa-tag me-1 text-primary"></i> ลักษณะการจัด</label>
                                <select class="form-select select2" id="attribute_id" name="attribute_id" required>
                                    <option value="" selected disabled>-- โปรดเลือก --</option>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM attribute ORDER BY attribute_id ASC");
                                        while ($attribute = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ($attribute['attribute_id'] == ($row['attribute_id'] ?? '')) ? "selected" : "";
                                            echo "<option value='{$attribute['attribute_id']}' $selected>{$attribute['attribute_name']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_id" class="form-label"><i class="fas fa-list-ul me-1 text-primary"></i> ประเภทกิจกรรม</label>
                                <select class="form-select select2" id="type_id" name="type_id" required>
                                    <option value="" <?php echo empty($row['type_id']) ? 'selected' : ''; ?> disabled>-- โปรดเลือก --</option>
                                    <?php
                                    try {
                                        $cultureStmt = $pdo->query("SELECT culture_id, culture_name FROM culture ORDER BY culture_id ASC");
                                        while ($culture = $cultureStmt->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ((string)$culture['culture_id'] === (string)($row['type_id'] ?? '')) ? 'selected' : '';
                                            $cultureId = htmlspecialchars((string)$culture['culture_id'], ENT_QUOTES, 'UTF-8');
                                            $cultureName = htmlspecialchars((string)$culture['culture_name'], ENT_QUOTES, 'UTF-8');
                                            echo "<option value=\"{$cultureId}\" {$selected}>{$cultureName}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        error_log('Unable to load activity cultures: ' . $e->getMessage());
                                        echo '<option value="" disabled>ไม่สามารถโหลดประเภทกิจกรรมได้</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Card 2: รายละเอียดโครงการ ===== -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <span class="card-header-icon purple"><i class="fas fa-project-diagram"></i></span>
                            รายละเอียดโครงการ
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- ข้อมูลพื้นฐาน -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-info-circle"></i> ข้อมูลพื้นฐาน</div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="activity_name" class="form-label">ชื่อโครงการ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-archive"></i></span>
                                        <input type="text" class="form-control" id="activity_name" name="activity_name" required placeholder="โครงการฝึกทักษะอาชีพตัดผมชาย" value="<?php echo $row['activity_name'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="objective" class="form-label">วัตถุประสงค์</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-crosshairs"></i></span>
                                        <input type="text" class="form-control" id="objective" name="objective" required placeholder="เพื่อให้ความรู้ในการประกอบอาชีพตัดผมชาย" value="<?php echo $row['objective'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="lecturer_id" class="form-label">วิทยากร</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <select class="form-select" id="lecturer_id" name="lecturer_id" required>
                                            <option value="" selected disabled>-- โปรดเลือก --</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->prepare("SELECT * FROM lecturer WHERE school_id = :school_id ORDER BY lecturer_id DESC");
                                                $stmt->execute(['school_id' => $_SESSION['ss_school_id']]);
                                                while ($lecturer = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($lecturer['lecturer_id'] == ($row['lecturer_id'] ?? '')) ? "selected" : "";
                                                    echo "<option value='{$lecturer['lecturer_id']}' $selected>{$lecturer['firstname']} {$lecturer['lastname']} | {$lecturer['talent']}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "Error: " . $e->getMessage();
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- สถานที่จัดกิจกรรม -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> สถานที่จัดกิจกรรม</div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="place" class="form-label">สถานที่จัด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="place" name="place" required placeholder="ห้องประชุมอาคาร... (เฉพาะสถานที่)" value="<?php echo $row['place'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="addr_detail" class="form-label">ที่อยู่ (บ้านเลขที่ / หมู่ / ถนน)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map"></i></span>
                                        <input type="text" class="form-control" id="addr_detail" name="addr_detail" autocomplete="off" placeholder="เช่น 118/39 ถนนสุขุมวิท" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="addr_province" class="form-label">จังหวัด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                        <select class="form-select" id="addr_province" name="addr_province">
                                            <option value="">-- โปรดเลือก --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="addr_amphur" class="form-label">อำเภอ/เขต</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                        <select class="form-select" id="addr_amphur" name="addr_amphur">
                                            <option value="">-- โปรดเลือก --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="addr_tambon" class="form-label">ตำบล/แขวง</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-location-dot"></i></span>
                                        <select class="form-select" id="addr_tambon" name="addr_tambon">
                                            <option value="">-- โปรดเลือก --</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="address" id="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">ละติจูด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <input type="text" class="form-control" id="latitude" name="latitude" required maxlength="10" placeholder="13.7563" value="<?php echo $row['latitude'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longtitude" class="form-label">ลองติจูด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <input type="text" class="form-control" id="longtitude" name="longtitude" required maxlength="11" placeholder="100.5018" value="<?php echo $row['longtitude'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="skill_id" class="form-label">Skill</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-solid fa-kitchen-set"></i></span>
                                        <select class="form-select" id="skill_id" name="skill_id" required>
                                            <option value="" selected disabled>-- โปรดเลือก --</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->prepare("SELECT * FROM skill ORDER BY skill_id DESC");
                                                $stmt->execute();
                                                while ($skill = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($skill['skill_id'] == ($row['skill_id'] ?? '')) ? "selected" : "";
                                                    echo "<option value='{$skill['skill_id']}' $selected>{$skill['skill_name']}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "Error: " . $e->getMessage();
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Card 3: กำหนดการจัดกิจกรรม ===== -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <span class="card-header-icon pink"><i class="fas fa-calendar-alt"></i></span>
                            กำหนดการจัดกิจกรรม
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-clock"></i> เลือกรูปแบบวันที่</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <select class="form-select select2" id="date_id" name="date_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM date ORDER BY date_id ASC");
                                            while ($date = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = ($date['date_id'] == ($row['date_id'] ?? '')) ? "selected" : "";
                                                echo "<option value='{$date['date_id']}' $selected>{$date['date_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- โหมด: ช่วงวันที่ -->
                            <div class="row" id="date_start_end_show">
                                <div class="col-md-6 mb-3">
                                    <label for="date_start" class="form-label">วันที่เริ่มจัด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="text" class="form-control datepicker" id="date_start" name="date_start" autocomplete="off" value="<?php echo $row['date_start'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_end" class="form-label">วันที่สิ้นสุด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="text" class="form-control datepicker" id="date_end" name="date_end" autocomplete="off" value="<?php echo $row['date_end'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- โหมด: หลายวัน -->
                            <div class="mb-3" id="date_range_show">
                                <label for="date_range" class="form-label">วันที่จัดกิจกรรม</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control" id="date_range" name="date_range" autocomplete="off" readonly value="<?php echo $row['date_range'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-hourglass-half"></i> ช่วงเวลา</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="time_start" class="form-label">เวลาเริ่มจัด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        <input type="text" class="form-control timepicker" id="time_start" name="time_start" autocomplete="off" required value="<?php echo $row['time_start'] ?? '08:30'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="time_end" class="form-label">เวลาสิ้นสุด</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        <input type="text" class="form-control timepicker" id="time_end" name="time_end" autocomplete="off" required value="<?php echo $row['time_end'] ?? '17:00'; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Card 4: งบประมาณและค่าใช้จ่าย ===== -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <span class="card-header-icon orange"><i class="fas fa-wallet"></i></span>
                            งบประมาณและค่าใช้จ่าย
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-coins"></i> ข้อมูลงบประมาณ</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="year_id" class="form-label">ปีงบประมาณ</label>
                                    <select class="form-select select2" id="year_id" name="year_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM year ORDER BY year_id ASC");
                                            while ($year = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = ($year['year_id'] == ($row['year_id'] ?? '')) ? "selected" : "";
                                                echo "<option value='{$year['year_id']}' $selected>ปีงบประมาณ {$year['year_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="plan_id" class="form-label">ค่าใช้จ่ายกิจกรรม</label>
                                    <select class="form-select select2" id="plan_id" name="plan_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM plan ORDER BY plan_id ASC");
                                            while ($plan = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = ($plan['plan_id'] == ($row['plan_id'] ?? '')) ? "selected" : "";
                                                echo "<option value='{$plan['plan_id']}' $selected>{$plan['plan_name_show']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ===== ส่วน: ระดับหลักสูตร & ค่าตอบแทนวิทยากร (เลือกได้ 1 ระดับ) ===== -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-layer-group"></i> ระดับหลักสูตร & ค่าตอบแทนวิทยากร
                            </div>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                เลือกระดับที่จัด <b>เพียง 1 ระดับ</b> แล้วกรอกค่าตอบแทนวิทยากร
                                (ค่าเริ่มต้น: พื้นฐาน 300, ชั้นกลาง 400, ชั้นสูง 600 บาท — แก้ไขได้)
                            </p>

                            <?php
                            $levelFields = [
                                ['key' => 'level_basic',        'label' => 'พื้นฐาน',  'icon' => 'fa-seedling', 'default' => 300, 'badge' => 'Basic'],
                                ['key' => 'level_intermediate', 'label' => 'ชั้นกลาง', 'icon' => 'fa-leaf',     'default' => 400, 'badge' => 'Intermediate'],
                                ['key' => 'level_advanced',     'label' => 'ชั้นสูง',  'icon' => 'fa-tree',     'default' => 600, 'badge' => 'Advanced'],
                            ];
                            ?>

                            <div class="level-grid">
                                <?php foreach ($levelFields as $lv):
                                    $chkKey   = $lv['key'];
                                    $rateKey  = $lv['key'] . '_rate';
                                    $isChecked = !empty($row[$chkKey]);
                                    $rateVal  = $isChecked && isset($row[$rateKey]) && $row[$rateKey] > 0
                                                    ? $row[$rateKey]
                                                    : $lv['default'];
                                ?>
                                    <div class="level-card <?= $isChecked ? 'active' : '' ?>" data-level="<?= $chkKey ?>">
                                        <div class="form-check d-flex justify-content-between align-items-center">
                                            <div>
                                                <input class="form-check-input level-check"
                                                       type="checkbox"
                                                       id="<?= $chkKey ?>"
                                                       name="<?= $chkKey ?>"
                                                       value="1"
                                                       data-rate-target="<?= $rateKey ?>"
                                                       data-default="<?= $lv['default'] ?>"
                                                       <?= $isChecked ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="<?= $chkKey ?>">
                                                    <i class="fas <?= $lv['icon'] ?> text-primary"></i>
                                                    <?= $lv['label'] ?>
                                                </label>
                                            </div>
                                            <span class="level-badge"><?= $lv['badge'] ?></span>
                                        </div>

                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                            <input type="text"
                                                   class="form-control level-rate"
                                                   id="<?= $rateKey ?>"
                                                   name="<?= $rateKey ?>"
                                                   autocomplete="off"
                                                   maxlength="10"
                                                   value="<?= htmlspecialchars($rateVal) ?>"
                                                   <?= $isChecked ? '' : 'disabled' ?>
                                                   placeholder="<?= $lv['default'] ?>">
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- ===== ส่วน: ผู้ยืมเงิน ===== -->
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-hand-holding-usd"></i> ผู้ยืมเงิน</div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="borrower_id" class="form-label">
                                        <i class="fas fa-chalkboard-user me-1 text-primary"></i> เลือกครูผู้ยืมเงิน
                                    </label>
                                    <select class="form-select select2" id="borrower_id" name="borrower_id" required>
                                        <option value="" selected disabled>-- โปรดเลือกครู --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("
                                                SELECT school_id, school_name, teacher_name
                                                FROM school
                                                WHERE teacher_name IS NOT NULL
                                                  AND TRIM(teacher_name) <> ''
                                                ORDER BY teacher_name ASC
                                            ");

                                            $seen = [];

                                            while ($school = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $teacherName = trim($school['teacher_name'] ?? '');
                                                if ($teacherName === '') continue;

                                                $names = preg_split('/[,;\r\n]+/', $teacherName);

                                                foreach ($names as $name) {
                                                    $name = trim($name);
                                                    if ($name === '') continue;
                                                    if (isset($seen[$name])) continue;
                                                    $seen[$name] = true;

                                                    $schoolName = trim($school['school_name'] ?? '');
                                                    $label = $schoolName !== ''
                                                        ? "{$name}  ({$schoolName})"
                                                        : $name;

                                                    $selected = ($name === ($row['borrower_id'] ?? '')) ? "selected" : "";
                                                    echo "<option value='" . htmlspecialchars($name) . "' $selected>"
                                                         . htmlspecialchars($label) .
                                                         "</option>";
                                                }
                                            }
                                        } catch (PDOException $e) {
                                            echo "<option disabled>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="borrower-info-box">
                                        <i class="fas fa-info-circle me-1"></i>
                                        รายการค่าใช้จ่ายที่ติ๊ก <b>"เครดิต"</b> จะ<b>ไม่ถูกนำมาคำนวณรวม</b>เป็นยอดที่ขอยืม
                                        (ใช้สำหรับรายการที่ทางร้านค้าให้เครดิต / ไม่ต้องจ่ายสด)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-receipt"></i> รายละเอียดค่าใช้จ่าย</div>
                            <div class="row cost-grid">

                                <?php
                                $costFields = [
                                    ['key' => 'lecturer_cost', 'label' => 'ค่าวิทยากร',     'icon' => 'fa-chalkboard-teacher', 'show_id' => ''],
                                    ['key' => 'material_cost', 'label' => 'ค่าวัสดุ',        'icon' => 'fa-box',                'show_id' => ''],
                                    ['key' => 'board_cost',    'label' => 'ค่าป้าย',         'icon' => 'fa-sign',               'show_id' => 'board_cost_show'],
                                    ['key' => 'food_cost',     'label' => 'ค่าอาหาร',        'icon' => 'fa-utensils',           'show_id' => 'food_cost_show'],
                                    ['key' => 'drink_cost',    'label' => 'ค่าอาหารว่าง',    'icon' => 'fa-coffee',             'show_id' => 'drink_cost_show'],
                                    ['key' => 'place_cost',    'label' => 'ค่าสถานที่',      'icon' => 'fa-building',           'show_id' => 'place_cost_show'],
                                    ['key' => 'car_cost',      'label' => 'ค่าจ้างเหมารถ',   'icon' => 'fa-bus',                'show_id' => 'car_cost_show'],
                                    ['key' => 'other_cost',    'label' => 'ค่าใช้จ่ายอื่น',  'icon' => 'fa-ellipsis-h',         'show_id' => 'other_cost_show'],
                                ];

                                foreach ($costFields as $c):
                                    $val       = $row[$c['key']] ?? '';
                                    $creditKey = $c['key'] . '_credit';
                                    $isCredit  = !empty($row[$creditKey]);
                                    $wrapperId = $c['show_id'] !== '' ? "id=\"{$c['show_id']}\"" : '';
                                ?>
                                    <div class="col-md-4 mb-3" <?= $wrapperId ?>>
                                        <div class="cost-label-row">
                                            <label for="<?= $c['key'] ?>" class="form-label mb-0">
                                                <i class="fas <?= $c['icon'] ?> me-1 text-primary"></i> <?= $c['label'] ?>
                                            </label>
                                            <label class="credit-switch" for="<?= $creditKey ?>" title="ติ๊กถ้าเป็นรายการเครดิต (ไม่นับรวมยอดยืม)">
                                                <input type="checkbox"
                                                       class="credit-check"
                                                       id="<?= $creditKey ?>"
                                                       name="<?= $creditKey ?>"
                                                       value="1"
                                                       data-target="<?= $c['key'] ?>"
                                                       <?= $isCredit ? 'checked' : '' ?>>
                                                <i class="fas fa-credit-card"></i> เครดิต
                                            </label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control cost-input <?= $isCredit ? 'is-credit' : '' ?>"
                                                   id="<?= $c['key'] ?>"
                                                   name="<?= $c['key'] ?>"
                                                   autocomplete="off"
                                                   maxlength="10"
                                                   required
                                                   value="<?= htmlspecialchars($val) ?>">
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- รวมที่ขอยืม -->
                                <div class="col-md-4 mb-3">
                                    <label for="total_costs" class="form-label">
                                        <i class="fas fa-calculator me-1 text-primary"></i> รวมที่ขอยืม (ไม่รวมเครดิต)
                                    </label>
                                    <div class="total-cost-box">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="total_costs" name="total_costs"
                                                   autocomplete="off" required readonly
                                                   value="<?php echo $row['total_costs'] ?? ''; ?>">
                                            <span class="input-group-text">บาท</span>
                                        </div>
                                    </div>
                                    <div class="credit-summary">
                                        <span><i class="fas fa-credit-card me-1"></i> ยอดเครดิต</span>
                                        <span><span id="credit_total">0.00</span> บาท</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ปุ่ม Action -->
                        <div class="form-actions d-flex justify-content-center gap-3 flex-wrap">
                            <button type="submit" class="btn-modern-primary" onclick="return validateForm();">
                                <i class="fas fa-save"></i> บันทึกข้อมูล
                            </button>
                            <button type="button" class="btn-modern-secondary" onclick="window.location='act_main.php';return false;">
                                <i class="fas fa-times"></i> ยกเลิก
                            </button>
                            <input type="hidden" name="activity_id" value="<?php echo $edit_id; ?>">
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>
<!-- timepicker plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').each(function() {
        var $el = $(this);
        var opts = {
            placeholder: '-- โปรดเลือก --',
            width: '100%'
        };
        if ($el.closest('.input-group').length > 0) {
            opts.dropdownParent = 'body';
        }
        $el.select2(opts);
    });

    function ADYearToBEYear(dateString) {
        if (!dateString) return '';
        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString;
        let year = parseInt(parts[2]);
        if (year < 2400) year += 543;
        return `${parts[0]}/${parts[1]}/${year}`;
    }

    function BEYearToADYear(dateString) {
        if (!dateString) return '';
        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString;
        let year = parseInt(parts[2]);
        if (year > 2400) year -= 543;
        return `${parts[0]}/${parts[1]}/${year}`;
    }

    // Flatpickr - date range
    const dateRangePicker = flatpickr("#date_range", {
        locale: 'th',
        mode: "multiple",
        dateFormat: "d/m/Y",
        formatDate: function(date, format) {
            const day = date.getDate();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear() + 543;
            return `${day}/${month}/${year}`;
        },
        parseDate: function(dateString, format) {
            const parts = dateString.split("/");
            if (parts.length === 3) {
                const day = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10) - 1;
                let year = parseInt(parts[2], 10);
                if (year > 2400) year -= 543;
                return new Date(year, month, day);
            }
            return null;
        },
        onReady: function(selectedDates, dateStr, instance) { fixFlatpickrYearToBE(instance); },
        onMonthChange: function(selectedDates, dateStr, instance) { fixFlatpickrYearToBE(instance); },
        onYearChange: function(selectedDates, dateStr, instance) { fixFlatpickrYearToBE(instance); },
        onOpen: function(selectedDates, dateStr, instance) { fixFlatpickrYearToBE(instance); },
        onChange: function(selectedDates, dateStr, instance) {
            $("#date_range").val(dateStr);
            if (dateStr) {
                $("#date_start").val('');
                $("#date_end").val('');
            }
        }
    });

    // แปลงค่าเริ่มต้นจาก DB ให้แสดงเป็น พ.ศ.
    setTimeout(function() {
        const input = document.getElementById('date_range');
        if (input && input.value) {
            const parts = input.value.split(',');
            const converted = parts.map(function(p) {
                p = p.trim();
                const dparts = p.split('/');
                if (dparts.length === 3) {
                    let y = parseInt(dparts[2], 10);
                    if (y && y < 2500) y += 543;
                    return `${dparts[0]}/${dparts[1]}/${y}`;
                }
                return p;
            }).join(', ');
            input.value = converted;
        }
    }, 120);

    function fixFlatpickrYearToBE(instance) {
        setTimeout(function() {
            if (!instance || !instance.calendarContainer) return;
            const currentMonth = instance.calendarContainer.querySelector('.flatpickr-current-month');
            if (currentMonth) {
                const yearEl = currentMonth.querySelector('.cur-year');
                if (yearEl) {
                    const adYear = parseInt(yearEl.textContent || yearEl.value, 10);
                    if (adYear && adYear < 2500) {
                        yearEl.textContent = (adYear + 543).toString();
                    }
                }
            }
            const yearInput = instance.currentYearElement;
            if (yearInput) {
                const adYear = parseInt(yearInput.value, 10);
                if (adYear && adYear < 2500) {
                    yearInput.value = (adYear + 543).toString();
                }
            }
        }, 30);
    }

    // Timepicker
    $('.timepicker').each(function() {
        let defaultTime = $(this).val();
        if (!defaultTime) {
            defaultTime = $(this).attr('id') === 'time_start' ? '08:30' : '17:00';
        }
        $(this).timepicker({
            timeFormat: 'HH:mm',
            interval: 30,
            minTime: '00:00',
            maxTime: '23:59',
            defaultTime: defaultTime,
            startTime: '00:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
    });

    // Datepicker (jQuery UI)
    var datepickerElements = $('.datepicker');
    if (datepickerElements.length > 0) {
        $.datepicker.regional['th'] = {
            closeText: 'ปิด',
            prevText: '<ย้อน',
            nextText: 'ถัดไป>',
            currentText: 'วันนี้',
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
            monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
            dayNames: ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
            dayNamesShort: ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'],
            dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            weekHeader: 'Wk',
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            beforeShow: function() {
                setTimeout(function() {
                    $.each($(".ui-datepicker-year option"), function(j, k) {
                        var textYear = parseInt($(k).text()) + 543;
                        $(k).text(textYear);
                    });
                }, 50);
            }
        };
        $.datepicker.setDefaults($.datepicker.regional['th']);

        datepickerElements.datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+1',
            onSelect: function(dateText, inst) {
                const parts = dateText.split("/");
                const day = parts[0];
                const month = String(parts[1]).padStart(2, '0');
                const year = parts[2];
                $(this).val(ADYearToBEYear(`${day}/${month}/${year}`));
            },
            onChangeMonthYear: function(year, month, inst) {
                setTimeout(function() {
                    $.each($(".ui-datepicker-year option"), function(j, k) {
                        var textYear = parseInt($(k).text()) + 543;
                        $(k).text(textYear);
                    });
                    const monthElement = $('.ui-datepicker-month');
                    monthElement.each(function() {
                        $(this).text(String(parseInt($(this).text()) + 1).padStart(2, '0'));
                    });
                }, 50);
            }
        });
    }

    // Cleave for lat/long
    new Cleave('#latitude', {
        numeral: true,
        numeralPositiveOnly: true,
        numeralDecimalScale: 7,
        numeralThousandsGroupStyle: 'none'
    });
    new Cleave('#longtitude', {
        numeral: true,
        numeralPositiveOnly: true,
        numeralDecimalScale: 7,
        numeralThousandsGroupStyle: 'none'
    });
    new Cleave('#date_start', { date: true, delimiter: '/', datePattern: ['d', 'm', 'Y'] });
    new Cleave('#date_end',   { date: true, delimiter: '/', datePattern: ['d', 'm', 'Y'] });

    // Cleave for cost fields
    document.querySelectorAll("input[id$='_cost']").forEach(function(ex) {
        new Cleave(ex, {
            numeral: true,
            numeralPositiveOnly: true,
            numeralThousandsGroupStyle: 'thousand',
            onValueChanged: function(e) {
                if (e.target.rawValue === '') e.target.element.value = '0';
            }
        });
    });
    new Cleave('#total_costs', {
        numeral: true,
        numeralPositiveOnly: true,
        numeralThousandsGroupStyle: 'thousand',
        onValueChanged: function(e) {
            if (e.target.rawValue === '') e.target.element.value = '0';
        }
    });

    // Cleave for level rate fields
    document.querySelectorAll('.level-rate').forEach(function(el) {
        new Cleave(el, {
            numeral: true,
            numeralPositiveOnly: true,
            numeralThousandsGroupStyle: 'thousand'
        });
    });

    $('#latitude, #longtitude').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });

    $(".cost-input").on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        sumTotalCost();
    });

    // ผูก event ให้ checkbox เครดิต
    $(document).on('change', '.credit-check', function() {
        const targetId = $(this).data('target');
        const $input = $('#' + targetId);
        if ($(this).is(':checked')) {
            $input.addClass('is-credit');
        } else {
            $input.removeClass('is-credit');
        }
        sumTotalCost();
    });

    // ===== Level Switch (พื้นฐาน / ชั้นกลาง / ชั้นสูง) — เลือกได้ 1 ระดับเท่านั้น =====
    $(document).on('change', '.level-check', function() {
        const $current = $(this);
        const $card    = $current.closest('.level-card');
        const target   = $current.data('rate-target');
        const def      = $current.data('default');
        const $input   = $('#' + target);

        if ($current.is(':checked')) {
            // ✨ ยกเลิกการติ๊กของระดับอื่น ๆ ทั้งหมดก่อน
            $('.level-check').not($current).each(function() {
                const otherTarget = $(this).data('rate-target');
                $(this).prop('checked', false);
                $(this).closest('.level-card').removeClass('active');
                $('#' + otherTarget).prop('disabled', true).val(0);
            });

            // เปิดใช้งานระดับที่เลือก
            $card.addClass('active');
            $input.prop('disabled', false);

            // ถ้ายังว่าง / เป็น 0 ให้เติมค่า default
            const cur = Number(($input.val() || '').toString().replace(/,/g, ''));
            if (!cur || cur === 0) {
                $input.val(def);
            }

            // โฟกัสที่ช่องกรอกเงิน เพื่อให้ผู้ใช้แก้ไขได้ทันที
            setTimeout(function() {
                $input.trigger('focus').select();
            }, 50);
        } else {
            // กรณีติ๊กออก
            $card.removeClass('active');
            $input.prop('disabled', true).val(0);
        }
    });

    // คลิกที่การ์ดทั้งใบเพื่อ toggle ระดับ (สะดวกขึ้น)
    $(document).on('click', '.level-card', function(e) {
        // ถ้าคลิกที่ checkbox / label / input ปล่อยทำงานปกติ
        if ($(e.target).is('input, label, .form-check-input, .form-check-label, .level-rate')) return;

        const $chk = $(this).find('.level-check');
        $chk.prop('checked', !$chk.is(':checked')).trigger('change');
    });

    // กรอกได้เฉพาะตัวเลขและจุดทศนิยม (level rate)
    $(document).on('input', '.level-rate', function() {
        this.value = this.value.replace(/[^0-9.,]/g, '').replace(/(\..*)\./g, '$1');
    });

    $('#date_id').on('change', function() {
        if (this.value == '1') {
            $('#date_range_show').hide();
            $('#date_start_end_show').show();
        } else {
            $('#date_range_show').show();
            $('#date_start_end_show').hide();
        }
        $('#date_range').val('');
        $(".datepicker").datepicker('setDate', null);
    });

    $('#format_id').on('change', function() {
        if (this.value == '1') {
            $('[id$=_cost_show]').show();
        } else {
            $('[id$=_cost_show]').hide();
            $('[id$=_cost_show] input').val('0');
        }
        sumTotalCost();
    });

    ShowActivity();
    sumTotalCost(); // คำนวณยอดเริ่มต้นตอนโหลดหน้า (กรณีแก้ไข)

    // ===== ที่อยู่: จังหวัด / อำเภอ / ตำบล (cascading จากข้อมูลสถานศึกษา) =====
    loadAddressOptions('get_address.php?action=provinces', $('#addr_province'), '-- โปรดเลือก --');

    $('#addr_province').on('change', function() {
        var province = this.value;
        resetAddressSelect($('#addr_amphur'));
        resetAddressSelect($('#addr_tambon'));
        if (province) {
            loadAddressOptions('get_address.php?action=amphures&province=' + encodeURIComponent(province), $('#addr_amphur'), '-- โปรดเลือก --');
        }
        composeAddress();
    });

    $('#addr_amphur').on('change', function() {
        var province = $('#addr_province').val();
        var amphur = this.value;
        resetAddressSelect($('#addr_tambon'));
        if (province && amphur) {
            loadAddressOptions('get_address.php?action=tambons&province=' + encodeURIComponent(province) + '&amphur=' + encodeURIComponent(amphur), $('#addr_tambon'), '-- โปรดเลือก --');
        }
        composeAddress();
    });

    $('#addr_tambon').on('change', composeAddress);
    $('#addr_detail').on('input', composeAddress);
});

function escHtml(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function resetAddressSelect($select) {
    $select.html('<option value="">-- โปรดเลือก --</option>');
}

function loadAddressOptions(url, $select, placeholder) {
    $.getJSON(url, function(data) {
        var html = '<option value="">' + escHtml(placeholder) + '</option>';
        if (Array.isArray(data)) {
            data.forEach(function(item) {
                var name = (item && item.name_th != null) ? item.name_th : item;
                if (name) {
                    html += '<option value="' + escHtml(name) + '">' + escHtml(name) + '</option>';
                }
            });
        }
        $select.html(html);
    });
}

function composeAddress() {
    var detail   = ($('#addr_detail').val() || '').trim();
    var tambon   = $('#addr_tambon').val();
    var amphur   = $('#addr_amphur').val();
    var province = $('#addr_province').val();
    var parts = [];
    if (detail)   parts.push(detail);
    if (tambon)   parts.push('ต.' + tambon);
    if (amphur)   parts.push('อ.' + amphur);
    if (province) parts.push('จ.' + province);
    $('#address').val(parts.join(' ').replace(/\s+/g, ' ').trim());
}

function ShowActivity() {
    if ($("#format_id").val() == 2 || $("#format_id").val() == 3) {
        $("div[id$='_cost_show']").hide();
        $("div[id$='_cost_show'] input").val(0);
    }
    if ($("#date_id").val() == 1) {
        $("#date_range_show").hide();
        $("#date_start_end_show").show();
    } else if ($("#date_id").val() == 2) {
        $("#date_start_end_show").hide();
        $("#date_range_show").show();
    }
}

function sumTotalCost() {
    let total  = 0;
    let credit = 0;

    $(".cost-input").each(function(i, el) {
        let val = Number(el.value.replace(/,/g, ""));
        if (isNaN(val)) val = 0;

        const $chk = $('.credit-check[data-target="' + el.id + '"]');
        const isCredit = $chk.is(':checked');

        if (isCredit) {
            credit += val;
        } else {
            total  += val;
        }
    });

    const fmt = (n) => n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    $('#total_costs').val(fmt(total));
    $('#credit_total').text(fmt(credit));
}

function validateForm() {
    // รวมบ้านเลขที่ + ตำบล/อำเภอ/จังหวัด ลงในช่อง address ที่จะส่งไปบันทึก
    composeAddress();
    if (!$('#address').val()) {
        alert('กรุณากรอกหรือเลือกที่อยู่');
        $('#addr_detail').focus();
        return false;
    }

    let dateType = $('#date_id').val();
    if (dateType == 1) {
        let startDate = $('#date_start').val();
        let endDate = $('#date_end').val();
        if (!startDate || !endDate) {
            alert('กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด');
            return false;
        }
        $('#date_start').val(convertToAD(startDate));
        $('#date_end').val(convertToAD(endDate));
    } else if (dateType == 2) {
        let dateRangeBE = $('#date_range').val();
        if (!dateRangeBE) {
            alert('กรุณาเลือกวันที่จัดกิจกรรม');
            return false;
        }
        const dateRangeAD = convertDateRangeToAD(dateRangeBE);
        $('#date_range').val(dateRangeAD);
        $('#date_start').val('0000-00-00');
        $('#date_end').val('0000-00-00');
    }

    // ✨ ตรวจสอบว่าเลือกระดับหลักสูตรอย่างน้อย 1 ระดับ
    if ($('.level-check:checked').length === 0) {
        alert('กรุณาเลือกระดับหลักสูตร (พื้นฐาน / ชั้นกลาง / ชั้นสูง) อย่างน้อย 1 ระดับ');
        return false;
    }

    // ✨ ตรวจสอบว่ากรอกค่าตอบแทนวิทยากร > 0
    const $checkedLevel = $('.level-check:checked');
    const rateTarget = $checkedLevel.data('rate-target');
    const rateValue = Number(($('#' + rateTarget).val() || '').toString().replace(/,/g, ''));
    if (!rateValue || rateValue <= 0) {
        alert('กรุณากรอกค่าตอบแทนวิทยากรของระดับที่เลือก');
        $('#' + rateTarget).prop('disabled', false).focus();
        return false;
    }

    // เปิด disabled ของ level-rate ที่ไม่ได้ติ๊ก เพื่อให้ค่า 0 ส่งไปด้วย
    $('.level-rate').each(function() {
        if ($(this).is(':disabled')) {
            $(this).prop('disabled', false).val(0);
        }
    });

    return true;
}

function convertToAD(dateString) {
    if (!dateString) return '';
    const parts = dateString.split('/');
    if (parts.length !== 3) return dateString;
    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10);
    let year = parseInt(parts[2], 10);
    year = year - 543;
    return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
}

function convertDateRangeToAD(beRangeStr) {
    if (!beRangeStr) return '';
    return beRangeStr.split(',').map(function(d) {
        d = d.trim();
        const parts = d.split('/');
        if (parts.length !== 3) return d;
        let y = parseInt(parts[2], 10);
        if (y > 2400) y -= 543;
        return `${parts[0]}/${parts[1]}/${y}`;
    }).join(', ');
}
</script>
</body>
</html>
