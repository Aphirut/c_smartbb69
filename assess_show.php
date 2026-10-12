<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo '<meta http-equiv="refresh" content="2;URL=index.php" />';
    exit;
}

include "./includes/connect.php";
include "./includes/function.php";

/* =========================
   ตรวจสอบ act_id
========================= */
$act_id = $_GET['act_id'] ?? '';

if (!is_numeric($act_id)) {
    header("Location:index.php");
    exit;
}

/* =========================
   ตรวจสอบข้อมูลโครงการ
========================= */
try {
    $sql = "SELECT * 
            FROM activity 
            WHERE activity_id = :act_id 
            AND school_id = :school_id 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'act_id'    => $act_id,
        'school_id' => $_SESSION['ss_school_id']
    ]);

    if ($stmt->rowCount() == 0) {
        header("Location:index.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("assess_show.php (activity check) Error: " . $e->getMessage());
    header("Location:index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>รายละเอียดการประเมินผล - ระบบการศึกษาต่อเนื่อง</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --pink: #ec4899;
            --purple: #7c3aed;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(15,23,42,0.08);
            --shadow-lg: 0 10px 40px rgba(16,185,129,0.15);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%);
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
            color: var(--success);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        /* ===== Hero Header ===== */
        .doc-hero {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #4f46e5 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .doc-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .doc-hero::after {
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
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
            max-width: 700px;
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
        .hero-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: var(--success);
            padding: 0.65rem 1.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.25s ease;
            border: none;
        }
        .hero-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
            color: var(--success);
        }
        .hero-action-btn.disabled {
            background: rgba(255,255,255,0.3);
            color: white;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ===== Stats Pills Row ===== */
        .stats-row { margin-bottom: 1.5rem; }
        .stat-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.15rem 1.25rem;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 0.95rem;
            transition: all 0.3s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .stat-icon.bg-total { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stat-icon.bg-pass  { background: linear-gradient(135deg, #10b981, #06b6d4); }
        .stat-icon.bg-fail  { background: linear-gradient(135deg, #ef4444, #ec4899); }
        .stat-icon.bg-pending { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
            font-weight: 500;
        }

        /* ===== Section Card ===== */
        .section-card {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            overflow: hidden;
            animation: fadeInUp 0.4s ease-out;
            margin-bottom: 1.5rem;
        }
        .section-card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to right, #f0fdfa, white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .section-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .section-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.05rem;
        }
        .section-icon.purple { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .section-title-text h6 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }
        .section-title-text p {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* ===== Info Grid (Project Detail) ===== */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 0;
        }
        .info-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .info-item.full {
            grid-column: 1 / -1;
        }
        .info-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--bg-soft);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .info-item-icon.indigo { background: var(--primary-light); color: var(--primary); }
        .info-item-icon.cyan   { background: #ecfeff; color: var(--info); }
        .info-item-icon.amber  { background: #fffbeb; color: var(--warning); }
        .info-item-icon.pink   { background: #fdf2f8; color: var(--pink); }

        .info-item-body { flex: 1; min-width: 0; }
        .info-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            margin: 0 0 0.2rem 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 600;
            word-break: break-word;
            margin: 0;
            line-height: 1.4;
        }

        /* Alert info bar */
        .modern-alert {
            margin: 1rem 1.5rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .modern-alert .btn-close { margin-left: auto; }

        /* ===== Modern Table ===== */
        .modern-table {
            margin: 0 !important;
            border-collapse: separate;
            border-spacing: 0;
        }
        .modern-table thead th {
            background: #f8fafc !important;
            color: var(--text-dark) !important;
            font-weight: 700 !important;
            border: none !important;
            border-bottom: 2px solid var(--border-soft) !important;
            padding: 1rem 1.25rem !important;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            vertical-align: middle;
        }
        .modern-table tbody td {
            padding: 0.9rem 1rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle;
            font-size: 0.92rem;
        }
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        .modern-table tbody tr:hover {
            background-color: #f0fdfa !important;
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Row number */
        .row-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg-soft);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* Student Name */
        .student-name {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .student-text {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Score Badge */
        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
            padding: 0.45rem 0.85rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
        }
        .score-badge.has-score {
            background: linear-gradient(135deg, #ecfdf5, #ecfeff);
            color: var(--success);
        }
        .score-badge.no-score {
            background: var(--bg-soft);
            color: var(--text-muted);
        }

        /* Status Pill */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .status-pill.pass {
            background: #ecfdf5;
            color: var(--success);
        }
        .status-pill.fail {
            background: #fef2f2;
            color: var(--danger);
        }
        .status-pill.pending {
            background: #f1f5f9;
            color: var(--secondary);
        }

        /* ===== DataTables Custom Styling ===== */
        .dataTables_wrapper {
            padding: 1.25rem 1.5rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.5rem 0.9rem 0.5rem 2.25rem;
            font-size: 0.9rem;
            background: var(--bg-soft) url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 0.65rem center;
            min-width: 240px;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--success);
            box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
            outline: none;
            background-color: white;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid var(--border-soft);
            border-radius: 8px;
            padding: 0.35rem 0.65rem;
            background: white;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            border: none !important;
            padding: 0.35rem 0.75rem !important;
            margin: 0 2px !important;
            color: var(--text-dark) !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #ecfdf5 !important;
            color: var(--success) !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #10b981, #06b6d4) !important;
            color: white !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }

        /* Tooltip */
        .tooltip-inner {
            background: var(--text-dark);
            color: white;
            font-weight: 500;
            font-size: 0.78rem;
            padding: 0.4rem 0.65rem;
            border-radius: 8px;
        }
        .tooltip .tooltip-arrow::before { border-top-color: var(--text-dark); }

        /* When footer is placed inside .app-main */
        .app-main > .app-footer {
            margin-left: 0 !important;
            width: 100% !important;
            margin-top: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .doc-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.2rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .info-item { border-right: none; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .section-card,
        .stat-card { animation: fadeInUp 0.4s ease-out; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            /* =========================
               ดึงข้อมูลโครงการ + ผู้สอน
            ========================= */
            $detailSql = "SELECT 
                            activity.activity_id,
                            activity.activity_name,
                            course.course_name,
                            course.course_hour,
                            lecturer.firstname,
                            lecturer.lastname,
                            activity.place,
                            activity.address,
                            activity.date_id,
                            activity.date_range,
                            activity.date_start,
                            activity.date_end,
                            activity.time_start,
                            activity.time_end,
                            activity.total_study,
                            activity.total_graduate,
                            activity.status_line,
                            activity.status_assess
                        FROM activity
                        LEFT JOIN course
                            ON activity.course_id = course.course_id
                        LEFT JOIN lecturer
                            ON activity.lecturer_id = lecturer.lecturer_id
                        WHERE activity.activity_id = :act_id
                          AND activity.school_id = :school_id
                        LIMIT 1";

            $row = null;
            $date = '';
            try {
                $stmt = $pdo->prepare($detailSql);
                $stmt->execute([
                    'act_id'    => $act_id,
                    'school_id' => $_SESSION['ss_school_id']
                ]);
                $row = $stmt->fetch();

                if ($row) {
                    if ($row['date_id'] == "1") {
                        if ($row['date_start'] == $row['date_end']) {
                            $date = ADYearToBEYearTH($row['date_start'], 2);
                        } else {
                            $date = ADYearToBEYearTH($row['date_start'], 2)
                                  . " ถึง " .
                                  ADYearToBEYearTH($row['date_end'], 2);
                        }
                    } elseif ($row['date_id'] == "2") {
                        $date = DateRangeToString($row['date_range'], 2);
                    } else {
                        $date = "";
                    }
                }
            } catch (PDOException $e) {
                error_log("assess_show.php (detail query) Error: " . $e->getMessage());
                $row = null;
            }

            // คำนวณสถิติผู้เรียน
            $total_study    = (int)($row['total_study']    ?? 0);
            $total_graduate = (int)($row['total_graduate'] ?? 0);
            $total_fail     = max(0, $total_study - $total_graduate);
            $is_assessed    = ($row && $row['status_assess'] == "1");
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="assess_main.php">การประเมินผลฯ</a></li>
                    <li class="breadcrumb-item active fw-semibold">รายละเอียดการประเมินผล</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                        <div class="hero-avatar">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div style="min-width: 0;">
                            <span class="mode-badge">
                                <i class="fas fa-chart-line"></i>
                                รายละเอียดการประเมินผล
                            </span>
                            <h1 class="hero-title mt-2">
                                <?= htmlspecialchars($row['activity_name'] ?? 'ไม่พบโครงการ'); ?>
                            </h1>
                            <p class="hero-subtitle">
                                <i class="fas fa-calendar-alt me-1"></i> <?= $date; ?>
                                &nbsp;•&nbsp;
                                <i class="far fa-clock me-1"></i> <?= $row['time_start'] ?? '--'; ?> - <?= $row['time_end'] ?? '--'; ?> น.
                            </p>
                        </div>
                    </div>
                    <?php if ($row && $row['status_line'] != "") { ?>
                        <a href="assess_in.php?act_id=<?= $row['activity_id']; ?>" class="hero-action-btn">
                            <i class="fas fa-edit"></i> บันทึกคะแนน
                        </a>
                    <?php } else { ?>
                        <span class="hero-action-btn disabled">
                            <i class="fas fa-lock"></i> ยังไม่อนุมัติ
                        </span>
                    <?php } ?>
                </div>
            </div>

            <!-- Stats Pills -->
            <div class="row g-3 stats-row">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-icon bg-total"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($total_study); ?></div>
                            <div class="stat-label">ผู้เรียนทั้งหมด</div>
                        </div>
                    </div>
                </div>
                <?php if ($is_assessed) { ?>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-pass"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <div class="stat-value"><?= number_format($total_graduate); ?></div>
                                <div class="stat-label">ผ่านการประเมิน</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-fail"><i class="fas fa-times-circle"></i></div>
                            <div>
                                <div class="stat-value"><?= number_format($total_fail); ?></div>
                                <div class="stat-label">ไม่ผ่านการประเมิน</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-pending"><i class="fas fa-percentage"></i></div>
                            <div>
                                <div class="stat-value">
                                    <?= $total_study > 0 ? number_format(($total_graduate/$total_study)*100, 1) : '0'; ?>%
                                </div>
                                <div class="stat-label">อัตราผ่าน</div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-pending"><i class="fas fa-hourglass-half"></i></div>
                            <div>
                                <div class="stat-value"><?= number_format($total_study); ?></div>
                                <div class="stat-label">ยังไม่ประเมิน</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-pass"><i class="fas fa-info-circle"></i></div>
                            <div>
                                <div class="stat-value" style="font-size: 1rem;">รอการประเมินผล</div>
                                <div class="stat-label">กดปุ่ม "บันทึกคะแนน" เพื่อเริ่มประเมิน</div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- รายละเอียดโครงการ -->
            <?php if ($row) { ?>
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-title-wrap">
                        <div class="section-icon purple">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="section-title-text">
                            <h6>รายละเอียดข้อมูลโครงการ</h6>
                            <p>ข้อมูลหลักสูตร วิทยากร และสถานที่จัดกิจกรรม</p>
                        </div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-item-icon indigo"><i class="fas fa-book"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">หลักสูตร</p>
                            <p class="info-value">
                                <?= htmlspecialchars($row['course_name'] ?? ''); ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                                    <?= htmlspecialchars($row['course_hour'] ?? ''); ?> ชั่วโมง
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon pink"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">วิทยากร</p>
                            <p class="info-value">
                                <?= htmlspecialchars($row['firstname'] ?? ''); ?> <?= htmlspecialchars($row['lastname'] ?? ''); ?>
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon cyan"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">สถานที่จัด</p>
                            <p class="info-value"><?= htmlspecialchars($row['place'] ?? ''); ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fas fa-calendar-day"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">วันที่ดำเนินการ</p>
                            <p class="info-value"><?= $date; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon amber"><i class="far fa-clock"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">เวลาดำเนินการ</p>
                            <p class="info-value">
                                <?= htmlspecialchars($row['time_start'] ?? ''); ?> น. ถึง
                                <?= htmlspecialchars($row['time_end'] ?? ''); ?> น.
                            </p>
                        </div>
                    </div>

                    <div class="info-item full">
                        <div class="info-item-icon cyan"><i class="fas fa-home"></i></div>
                        <div class="info-item-body">
                            <p class="info-label">ที่อยู่</p>
                            <p class="info-value"><?= htmlspecialchars($row['address'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>

                <div class="modern-alert alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        <strong>แจ้งเตือน:</strong>
                        หากต้องการอัพเดท/บันทึกคะแนน กรุณากดปุ่ม <strong>บันทึกคะแนน</strong> ที่มุมขวาบน
                    </span>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php } ?>

            <!-- รายชื่อผู้เข้าร่วม + ผลการประเมิน -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-title-wrap">
                        <div class="section-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="section-title-text">
                            <h6>รายชื่อผู้เข้าร่วมโครงการ</h6>
                            <p>คะแนนและผลการประเมินของผู้เรียนแต่ละคน</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="example2" class="table modern-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ลำดับ</th>
                                <th>ชื่อ-สกุล</th>
                                <th style="width: 120px;">คะแนน</th>
                                <th style="width: 160px;">ผลการประเมิน</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 0;
                        $sql = "SELECT
                                    student.student_id,
                                    student.firstname,
                                    student.lastname,
                                    train.total_scores,
                                    train.status_assess
                                FROM train
                                INNER JOIN student
                                    ON train.student_id = student.student_id
                                WHERE train.activity_id = :act_id
                                  AND student.school_id = :school_id
                                ORDER BY student.student_id DESC";

                        try {
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                'act_id'    => $act_id,
                                'school_id' => $_SESSION['ss_school_id']
                            ]);

                            while ($stRow = $stmt->fetch()) {
                                $a++;
                                $firstChar = mb_substr($stRow['firstname'], 0, 1, 'UTF-8');
                                $hasScore = (isset($stRow['total_scores']) && $stRow['total_scores'] !== '' && $stRow['total_scores'] !== null);

                                echo "<tr>";
                                echo "<td class='text-center'><span class='row-number'>{$a}</span></td>";
                                echo "<td>
                                        <div class='student-name'>
                                            <div class='student-avatar'>{$firstChar}</div>
                                            <div class='student-text'>"
                                            . htmlspecialchars($stRow['firstname']) . " "
                                            . htmlspecialchars($stRow['lastname']) .
                                            "</div>
                                        </div>
                                      </td>";

                                // คะแนน
                                if ($hasScore) {
                                    echo "<td class='text-center'>
                                            <span class='score-badge has-score'>{$stRow['total_scores']}</span>
                                          </td>";
                                } else {
                                    echo "<td class='text-center'>
                                            <span class='score-badge no-score'>-</span>
                                          </td>";
                                }

                                // สถานะ
                                echo "<td class='text-center'>";
                                if ($stRow['status_assess'] == "1") {
                                    echo "<span class='status-pill pass'>
                                            <i class='fas fa-graduation-cap'></i> ผ่าน
                                          </span>";
                                } elseif ($stRow['status_assess'] == "0") {
                                    echo "<span class='status-pill fail'>
                                            <i class='fas fa-times-circle'></i> ไม่ผ่าน
                                          </span>";
                                } else {
                                    echo "<span class='status-pill pending'>
                                            <i class='fas fa-ban'></i> ยังไม่ประเมิน
                                          </span>";
                                }
                                echo "</td>";

                                echo "</tr>";
                            }

                            if ($a == 0) {
                                echo "<tr class='empty-row'><td colspan='4' class='text-center py-5 text-muted'>
                                        <i class='fas fa-user-slash fa-2x mb-2 d-block opacity-50'></i>
                                        ยังไม่มีผู้เข้าร่วมโครงการ
                                      </td></tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("assess_show.php (student list) Error: " . $e->getMessage());
                            echo "<tr><td colspan='4' class='text-center text-danger py-4'>
                                    <i class='fas fa-exclamation-triangle me-2'></i>
                                    เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
                                  </td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script>
$(function () {
    // Only init DataTable when there are real data rows
    var hasRealRows = $('#example2 tbody tr').length > 0 &&
                      !$('#example2 tbody td[colspan]').length;

    if (hasRealRows) {
        $('#example2').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ทั้งหมด"]],
            order: [],
            language: {
                search: "",
                searchPlaceholder: "🔍 ค้นหาผู้เรียน...",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                paginate: {
                    first: "« แรก",
                    last: "ท้าย »",
                    next: "ถัดไป ›",
                    previous: "‹ ก่อนหน้า"
                }
            },
            columnDefs: [
                { targets: [0, 2, 3], className: 'dt-center' }
            ]
        });
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
</body>
</html>