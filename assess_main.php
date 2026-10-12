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
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>การประเมินผลฯ - ระบบการศึกษาต่อเนื่อง</title>

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
        .hero-stat-pill {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 14px;
            padding: 0.75rem 1.25rem;
            color: white;
            text-align: center;
            min-width: 120px;
        }
        .hero-stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.78rem;
            opacity: 0.9;
            margin-top: 0.2rem;
        }

        /* ===== Legend Cards ===== */
        .legend-row { margin-bottom: 1.5rem; }
        .legend-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1rem 1.15rem;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .legend-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .legend-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        .legend-card.cat-form::before { background: linear-gradient(180deg, #4f46e5, #7c3aed); }
        .legend-card.cat-score::before { background: linear-gradient(180deg, #10b981, #06b6d4); }
        .legend-card.cat-assess::before { background: linear-gradient(180deg, #ef4444, #ec4899); }

        .legend-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .legend-card.cat-form .legend-icon { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .legend-card.cat-score .legend-icon { background: linear-gradient(135deg, #10b981, #06b6d4); }
        .legend-card.cat-assess .legend-icon { background: linear-gradient(135deg, #ef4444, #ec4899); }
        .legend-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.2;
        }
        .legend-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* ===== Main Table Card ===== */
        .table-card {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            overflow: hidden;
            animation: fadeInUp 0.4s ease-out;
        }
        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to right, #f0fdfa, white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .table-card-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .table-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.15rem;
        }
        .table-title-text h6 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }
        .table-title-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* ===== DataTable Styles ===== */
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

        /* Date Badge */
        .date-badge {
            background: linear-gradient(135deg, #ecfdf5, #ecfeff);
            color: var(--success);
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            line-height: 1.3;
        }

        /* Project Name */
        .project-name {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .project-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .project-text {
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.3;
        }
        .project-id {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== Action Buttons (Icon style) ===== */
        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }
        .action-btn-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            text-decoration: none;
            position: relative;
        }
        .action-btn-icon:hover {
            transform: translateY(-2px);
            color: white;
        }

        .ab-danger {
            background: #fef2f2;
            color: var(--danger);
        }
        .ab-danger:hover {
            background: var(--danger);
            box-shadow: 0 4px 12px rgba(239,68,68,0.35);
        }
        .ab-success {
            background: #ecfdf5;
            color: var(--success);
        }
        .ab-success:hover {
            background: var(--success);
            box-shadow: 0 4px 12px rgba(16,185,129,0.35);
        }
        .ab-warning {
            background: #fffbeb;
            color: var(--warning);
        }
        .ab-warning:hover {
            background: var(--warning);
            box-shadow: 0 4px 12px rgba(245,158,11,0.35);
        }
        .ab-info {
            background: #ecfeff;
            color: var(--info);
        }
        .ab-info:hover {
            background: var(--info);
            box-shadow: 0 4px 12px rgba(6,182,212,0.35);
        }
        .ab-primary {
            background: var(--primary-light);
            color: var(--primary);
        }
        .ab-primary:hover {
            background: var(--primary);
            box-shadow: 0 4px 12px rgba(79,70,229,0.35);
        }
        .ab-secondary {
            background: #f1f5f9;
            color: var(--secondary);
        }
        .ab-secondary:hover {
            background: var(--secondary);
            box-shadow: 0 4px 12px rgba(100,116,139,0.35);
        }
        .ab-pink {
            background: #fdf2f8;
            color: var(--pink);
        }
        .ab-pink:hover {
            background: var(--pink);
            box-shadow: 0 4px 12px rgba(236,72,153,0.35);
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

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.4;
        }

        /* Footer */
        .table-card-footer {
            background: var(--bg-soft);
            padding: 0.85rem 1.5rem;
            border-top: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 0.825rem;
        }

        /* Tooltip override */
        .tooltip-inner {
            background: var(--text-dark);
            color: white;
            font-weight: 500;
            font-size: 0.78rem;
            padding: 0.4rem 0.65rem;
            border-radius: 8px;
        }
        .tooltip .tooltip-arrow::before { border-top-color: var(--text-dark); }

        /* Responsive */
        @media (max-width: 768px) {
            .doc-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .table-card-header { flex-direction: column; align-items: flex-start; }
            .action-btn-icon { width: 34px; height: 34px; font-size: 0.85rem; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .legend-card,
        .table-card { animation: fadeInUp 0.4s ease-out; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            // นับจำนวนกิจกรรมทั้งหมด
            $totalActivities = 0;
            $school_id = $_SESSION['ss_school_id'];
            try {
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE school_id = :school_id");
                $countStmt->execute(['school_id' => $school_id]);
                $totalActivities = $countStmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("assess_main.php Count Error: " . $e->getMessage());
            }
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">การประเมินผลฯ</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-chart-line"></i>
                                ระบบประเมินผลกิจกรรม
                            </span>
                            <h1 class="hero-title mt-2">การประเมินผลฯ</h1>
                            <p class="hero-subtitle">พิมพ์ฟอร์มบันทึกคะแนน • บันทึกคะแนน • พิมพ์แบบประเมินความพึงพอใจ</p>
                        </div>
                    </div>
                    <div class="hero-stat-pill">
                        <div class="hero-stat-value"><?= number_format($totalActivities) ?></div>
                        <div class="hero-stat-label">โครงการทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- Legend / คำอธิบายหมวดหมู่ -->
            <div class="row g-3 legend-row">
                <div class="col-md-4">
                    <div class="legend-card cat-form">
                        <div class="legend-icon"><i class="fas fa-print"></i></div>
                        <div>
                            <h6 class="legend-title">ฟอร์มบันทึกคะแนน</h6>
                            <p class="legend-desc">พิมพ์ฟอร์มเปล่าสำหรับบันทึกคะแนน</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="legend-card cat-score">
                        <div class="legend-icon"><i class="fas fa-keyboard"></i></div>
                        <div>
                            <h6 class="legend-title">บันทึกคะแนน</h6>
                            <p class="legend-desc">บันทึกคะแนนผู้เรียนเข้าระบบ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="legend-card cat-assess">
                        <div class="legend-icon"><i class="fas fa-smile-beam"></i></div>
                        <div>
                            <h6 class="legend-title">ประเมินผล</h6>
                            <p class="legend-desc">พิมพ์แบบประเมินความพึงพอใจ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <div class="table-icon-wrap">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <div class="table-title-text">
                            <h6>รายการโครงการ / กิจกรรม</h6>
                            <p>คลิกที่ไอคอนเพื่อดำเนินการ • Hover เพื่อดูชื่อเอกสาร</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table modern-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ลำดับ</th>
                                <th style="width: 160px;">วันที่</th>
                                <th>ชื่อโครงการ</th>
                                <th style="width: 140px;">ฟอร์มบันทึกคะแนน</th>
                                <th style="width: 140px;">บันทึกคะแนน</th>
                                <th style="width: 140px;">ประเมินผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $a = 0;
                            try {
                                $sql = "SELECT activity.activity_id, activity.activity_name,
                                            activity.date_id, activity.date_range,
                                            activity.date_start, activity.date_end,
                                            activity.format_id
                                        FROM activity
                                        WHERE activity.school_id = :school_id ORDER BY activity_id DESC";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['school_id' => $school_id]);

                                while ($row = $stmt->fetch()) {
                                    $a++;
                                    $date = '';

                                    // ส่วนแสดงวันที่ (Logic เดิม + ขยายแบบหน้าเอกสารเปิดกลุ่ม)
                                    if ($row['date_id'] == "1") {
                                        if ($row['date_start'] == $row['date_end']) {
                                            $date = ADYearToBEYearTH(trim($row['date_start']), 1);
                                        } else {
                                            $start_date = trim($row['date_start']);
                                            $end_date = trim($row['date_end']);
                                            $start_parts = explode('-', $start_date);
                                            $end_parts = explode('-', $end_date);
                                            $start_year = $start_parts[0];
                                            $start_month = $start_parts[1];
                                            $start_day = $start_parts[2];
                                            $end_year = $end_parts[0];
                                            $end_month = $end_parts[1];
                                            $end_day = $end_parts[2];
                                            $thai_months = [
                                                '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
                                                '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
                                                '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
                                                '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                                            ];
                                            $thai_year = intval($start_year) + 543;
                                            $end_thai_year = intval($end_year) + 543;

                                            if ($start_year == $end_year && $start_month == $end_month) {
                                                $day_diff = intval($end_day) - intval($start_day);
                                                if ($day_diff <= 2 && $day_diff >= 0) {
                                                    $date = intval($start_day) . "-" . intval($end_day) . " " .
                                                            $thai_months[$start_month] . " " . $thai_year;
                                                } else {
                                                    $date = intval($start_day) . ", " . intval($end_day) . " " .
                                                            $thai_months[$start_month] . " " . $thai_year;
                                                }
                                            } else {
                                                if ($start_year == $end_year) {
                                                    $date = intval($start_day) . " " . $thai_months[$start_month] .
                                                            " - " . intval($end_day) . " " . $thai_months[$end_month] .
                                                            " " . $thai_year;
                                                } else {
                                                    $date = intval($start_day) . " " . $thai_months[$start_month] .
                                                            " " . $thai_year . " - " . intval($end_day) . " " .
                                                            $thai_months[$end_month] . " " . $end_thai_year;
                                                }
                                            }
                                        }
                                    } elseif ($row['date_id'] == "2") {
                                        $date = DateRangeToString($row['date_range'], 1);
                                    } else {
                                        $date = 'ไม่ระบุวันที่';
                                    }
                                    if (empty($date)) {
                                        $date = 'ไม่ระบุวันที่';
                                    }

                                    echo "<tr>";
                                    echo "<td class='text-center'><span class='row-number'>{$a}</span></td>";
                                    echo "<td class='text-center'><span class='date-badge'><i class='fas fa-calendar-alt'></i> {$date}</span></td>";
                                    echo "<td>
                                            <div class='project-name'>
                                                <div class='project-icon'><i class='fas fa-clipboard-list'></i></div>
                                                <div>
                                                    <div class='project-text'>" . htmlspecialchars($row['activity_name']) . "</div>
                                                    <div class='project-id'>รหัสโครงการ #{$row['activity_id']}</div>
                                                </div>
                                            </div>
                                          </td>";

                                    // === ฟอร์มบันทึกคะแนน ===
                                    echo "<td>";
                                    echo "<div class='action-group'>";
                                    echo "<a href='reports/assess_blank.php?act_id={$row['activity_id']}' target='_blank'
                                            class='action-btn-icon ab-primary'
                                            data-bs-toggle='tooltip' title='พิมพ์ฟอร์มบันทึกคะแนน'>
                                            <i class='fas fa-print'></i>
                                          </a>";
                                    echo "</div>";
                                    echo "</td>";

                                    // === บันทึกคะแนน ===
                                    echo "<td>";
                                    echo "<div class='action-group'>";
                                    echo "<a href='assess_show.php?act_id={$row['activity_id']}'
                                            class='action-btn-icon ab-success'
                                            data-bs-toggle='tooltip' title='บันทึกคะแนน'>
                                            <i class='fas fa-cog'></i>
                                          </a>";
                                    echo "</div>";
                                    echo "</td>";

                                    // === ประเมินผล ===
                                    echo "<td>";
                                    echo "<div class='action-group'>";
                                    echo "<a href='reports/assess.php?act_id={$row['activity_id']}' target='_blank'
                                            class='action-btn-icon ab-danger'
                                            data-bs-toggle='tooltip' title='แบบประเมินผลการฝึกอบรม'>
                                            <i class='fas fa-smile-beam'></i>
                                          </a>";
                                    echo "</div>";
                                    echo "</td>";

                                    echo "</tr>";
                                }

                                if ($a == 0) {
                                    echo "<tr class='empty-row'><td colspan='6'>
                                            <div class='empty-state'>
                                                <i class='fas fa-clipboard-check'></i>
                                                <h6 class='fw-semibold mb-1'>ยังไม่มีโครงการ/กิจกรรม</h6>
                                                <p class='small mb-0'>กรุณาเพิ่มโครงการในระบบก่อนเพื่อประเมินผล</p>
                                            </div>
                                          </td></tr>";
                                }
                            } catch (PDOException $e) {
                                error_log("assess_main.php Error: " . $e->getMessage());
                                echo "<tr><td colspan='6' class='text-center text-danger py-4'>
                                        <i class='fas fa-exclamation-triangle me-2'></i>
                                        เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
                                      </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>
                            <i class="fas fa-info-circle text-success me-1"></i>
                            คลิกที่ไอคอนเพื่อดำเนินการ • Hover เพื่อดูชื่อเอกสาร
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                            <i class="fas fa-clipboard-check me-1"></i>
                            ทั้งหมด <?= number_format($totalActivities) ?> โครงการ
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
$(document).ready(function() {
    // Only init DataTable when there are real data rows
    var hasRealRows = $('#example1 tbody tr').length > 0 &&
                      !$('#example1 tbody td[colspan]').length &&
                      !$('#example1 tbody tr.empty-row').length;

    if (hasRealRows) {
        $('#example1').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ทั้งหมด"]],
            order: [],
            language: {
                search: "",
                searchPlaceholder: "🔍 ค้นหาโครงการ...",
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
                { targets: [0, 1, 3, 4, 5], className: 'dt-center' },
                { targets: [3, 4, 5], orderable: false }
            ]
        });
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
</body>
</html>