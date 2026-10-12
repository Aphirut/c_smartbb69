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

$font_url = "https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap";
$link_home = "index.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>รายงานภาพการจัดฯ - ระบบการศึกษาต่อเนื่อง</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= $font_url ?>" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
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
            --shadow-lg: 0 10px 40px rgba(6,182,212,0.15);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #ecfeff 0%, #f8fafc 100%);
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
            color: var(--info);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        .doc-hero {
            background: linear-gradient(135deg, #06b6d4 0%, #4f46e5 50%, #7c3aed 100%);
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
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .doc-hero::after {
            content: '';
            position: absolute;
            bottom: -50%; left: -10%;
            width: 350px; height: 350px;
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
            width: 72px; height: 72px;
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
        .hero-stats {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .hero-stat-pill {
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px;
            padding: 0.6rem 1.1rem;
            color: white;
            text-align: center;
            min-width: 100px;
        }
        .hero-stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.75rem;
            opacity: 0.9;
            margin-top: 0.2rem;
        }

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
            top: 0; left: 0;
            width: 4px; height: 100%;
        }
        .legend-card.cat-record::before  { background: linear-gradient(180deg, #06b6d4, #4f46e5); }
        .legend-card.cat-status::before  { background: linear-gradient(180deg, #10b981, #06b6d4); }
        .legend-card.cat-report::before  { background: linear-gradient(180deg, #4f46e5, #7c3aed); }

        .legend-icon {
            width: 42px; height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .legend-card.cat-record .legend-icon { background: linear-gradient(135deg, #06b6d4, #4f46e5); }
        .legend-card.cat-status .legend-icon { background: linear-gradient(135deg, #10b981, #06b6d4); }
        .legend-card.cat-report .legend-icon { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
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
            background: linear-gradient(to right, #ecfeff, white);
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
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #06b6d4, #4f46e5);
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
            background-color: #ecfeff !important;
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .row-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border-radius: 8px;
            background: var(--bg-soft);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .date-badge {
            background: linear-gradient(135deg, #ecfeff, #eef2ff);
            color: var(--info);
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            line-height: 1.3;
        }

        .project-name {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .project-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #06b6d4, #4f46e5);
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

        /* ===== Status Icons (แทนรูปภาพ) ===== */
        .status-wrap {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border-radius: 50%;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: help;
        }
        .status-pill.done {
            background: #ecfdf5;
            color: var(--success);
            border: 2px solid #a7f3d0;
        }
        .status-pill.todo {
            background: #fffbeb;
            color: var(--warning);
            border: 2px solid #fde68a;
        }
        .status-pill:hover {
            transform: scale(1.1);
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }
        .action-btn-icon {
            width: 38px; height: 38px;
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

        .ab-primary { background: var(--primary-light); color: var(--primary); }
        .ab-primary:hover { background: var(--primary); box-shadow: 0 4px 12px rgba(79,70,229,0.35); }
        .ab-success { background: #ecfdf5; color: var(--success); }
        .ab-success:hover { background: var(--success); box-shadow: 0 4px 12px rgba(16,185,129,0.35); }
        .ab-danger { background: #fef2f2; color: var(--danger); }
        .ab-danger:hover { background: var(--danger); box-shadow: 0 4px 12px rgba(239,68,68,0.35); }
        .ab-info { background: #ecfeff; color: var(--info); }
        .ab-info:hover { background: var(--info); box-shadow: 0 4px 12px rgba(6,182,212,0.35); }
        .ab-purple { background: #faf5ff; color: var(--purple); }
        .ab-purple:hover { background: var(--purple); box-shadow: 0 4px 12px rgba(124,58,237,0.35); }

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
            border-color: var(--info);
            box-shadow: 0 0 0 4px rgba(6,182,212,0.12);
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
            background: #ecfeff !important;
            color: var(--info) !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #06b6d4, #4f46e5) !important;
            color: white !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(6,182,212,0.3);
        }

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

        .table-card-footer {
            background: var(--bg-soft);
            padding: 0.85rem 1.5rem;
            border-top: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 0.825rem;
        }

        .tooltip-inner {
            background: var(--text-dark);
            color: white;
            font-weight: 500;
            font-size: 0.78rem;
            padding: 0.4rem 0.65rem;
            border-radius: 8px;
        }
        .tooltip .tooltip-arrow::before { border-top-color: var(--text-dark); }

        @media (max-width: 768px) {
            .doc-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .table-card-header { flex-direction: column; align-items: flex-start; }
            .action-btn-icon { width: 34px; height: 34px; font-size: 0.85rem; }
            .status-pill { width: 28px; height: 28px; font-size: 0.78rem; }
        }

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
            // นับสถิติ
            $totalActivities = 0;
            $totalWithPhoto  = 0;
            $school_id = $_SESSION['ss_school_id'];
            try {
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE school_id = :school_id");
                $countStmt->execute(['school_id' => $school_id]);
                $totalActivities = $countStmt->fetchColumn();

                $photoStmt = $pdo->prepare("SELECT COUNT(*) FROM activity 
                                            LEFT JOIN reporting ON activity.activity_id = reporting.activity_id
                                            WHERE activity.school_id = :school_id 
                                            AND (reporting.photo_material_1 IS NOT NULL OR reporting.photo_activity_1 IS NOT NULL)
                                            AND (reporting.photo_material_1 != '' OR reporting.photo_activity_1 != '')");
                $photoStmt->execute(['school_id' => $school_id]);
                $totalWithPhoto = $photoStmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("reporting_main.php Count Error: " . $e->getMessage());
            }

            $totalNoPhoto = max(0, $totalActivities - $totalWithPhoto);
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= $link_home ?>"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">รายงานภาพการจัดฯ</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-camera-retro"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-images"></i>
                                ระบบบันทึกภาพกิจกรรม
                            </span>
                            <h1 class="hero-title mt-2">รายงานภาพการจัดฯ</h1>
                            <p class="hero-subtitle">บันทึกและจัดการภาพถ่ายวัสดุ • ภาพกิจกรรม • พิมพ์รายงานภาพ</p>
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($totalActivities); ?></div>
                            <div class="hero-stat-label">ทั้งหมด</div>
                        </div>
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($totalWithPhoto); ?></div>
                            <div class="hero-stat-label">มีภาพ</div>
                        </div>
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($totalNoPhoto); ?></div>
                            <div class="hero-stat-label">ยังไม่มี</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="row g-3 legend-row">
                <div class="col-md-4">
                    <div class="legend-card cat-record">
                        <div class="legend-icon"><i class="fas fa-camera"></i></div>
                        <div>
                            <h6 class="legend-title">บันทึก/ลบภาพ</h6>
                            <p class="legend-desc">บันทึกภาพและลบภาพทั้งหมด</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="legend-card cat-status">
                        <div class="legend-icon"><i class="fas fa-check-double"></i></div>
                        <div>
                            <h6 class="legend-title">สถานะภาพ</h6>
                            <p class="legend-desc">ภาพวัสดุ • ภาพกิจกรรม</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="legend-card cat-report">
                        <div class="legend-icon"><i class="fas fa-print"></i></div>
                        <div>
                            <h6 class="legend-title">พิมพ์รายงาน</h6>
                            <p class="legend-desc">รายงานภาพวัสดุ • ภาพกิจกรรม</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <div class="table-icon-wrap">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="table-title-text">
                            <h6>รายการบันทึกภาพกิจกรรม</h6>
                            <p>คลิกที่ไอคอนเพื่อบันทึก/ลบ/พิมพ์ • Hover เพื่อดูคำอธิบาย</p>
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
                                <th style="width: 130px;">บันทึก/ลบ</th>
                                <th style="width: 110px;">สถานะ</th>
                                <th style="width: 130px;">พิมพ์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $a = 0;
                            try {
                                $sql = "SELECT activity.activity_id, activity.activity_name,
                                            activity.date_id, activity.date_range,
                                            activity.date_start, activity.date_end,
                                            activity.format_id,
                                            reporting.photo_material_1, reporting.photo_activity_1
                                        FROM activity
                                        LEFT JOIN reporting ON activity.activity_id = reporting.activity_id
                                        WHERE activity.school_id = :school_id ORDER BY activity_id DESC";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['school_id' => $school_id]);

                                while ($row = $stmt->fetch()) {
                                    $a++;
                                    $date = '';

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

                                    $aid = $row['activity_id'];
                                    $aname = htmlspecialchars($row['activity_name']);
                                    $has_material = ($row['photo_material_1'] != "");
                                    $has_activity = ($row['photo_activity_1'] != "");

                                    // URLs
                                    $url_in   = "reporting_in.php?act_id=" . $aid;
                                    $url_del  = "reporting_del.php?submit=DEL&del_id=" . $aid;
                                    $url_pm   = "reports/reporting_photo_material.php?act_id=" . $aid;
                                    $url_pa   = "reports/reporting_photo_activity.php?act_id=" . $aid;

                                    // ===== ปุ่มบันทึกภาพ =====
                                    $btn_in = '<a href="' . $url_in . '" class="action-btn-icon ab-info" data-bs-toggle="tooltip" title="บันทึกภาพกิจกรรม"><i class="fas fa-camera"></i></a>';

                                    $btn_del = '';
                                    if ($has_material || $has_activity) {
                                        $btn_del = '<a href="' . $url_del . '" onClick="return CheckDel()" class="action-btn-icon ab-danger" data-bs-toggle="tooltip" title="ลบภาพทั้งหมด"><i class="fas fa-trash"></i></a>';
                                    }

                                    // ===== ปุ่มสถานะ (ใช้ icon แทนรูปภาพ) =====
                                    if ($has_material) {
                                        $status_m = '<a href="javascript:void(0)" class="status-pill done" data-bs-toggle="tooltip" title="รายงานภาพวัสดุแล้ว"><i class="fas fa-box"></i></a>';
                                    } else {
                                        $status_m = '<a href="javascript:void(0)" class="status-pill todo" data-bs-toggle="tooltip" title="ยังไม่รายงานภาพวัสดุ"><i class="fas fa-box"></i></a>';
                                    }

                                    if ($has_activity) {
                                        $status_a = '<a href="javascript:void(0)" class="status-pill done" data-bs-toggle="tooltip" title="รายงานภาพกิจกรรมแล้ว"><i class="fas fa-image"></i></a>';
                                    } else {
                                        $status_a = '<a href="javascript:void(0)" class="status-pill todo" data-bs-toggle="tooltip" title="ยังไม่รายงานภาพกิจกรรม"><i class="fas fa-image"></i></a>';
                                    }

                                    // ===== ปุ่มพิมพ์รายงาน =====
                                    $btn_pm = '<a href="' . $url_pm . '" target="_blank" class="action-btn-icon ab-info" data-bs-toggle="tooltip" title="รายงานภาพวัสดุ"><i class="fas fa-print"></i></a>';
                                    $btn_pa = '<a href="' . $url_pa . '" target="_blank" class="action-btn-icon ab-success" data-bs-toggle="tooltip" title="รายงานภาพกิจกรรม"><i class="fas fa-print"></i></a>';

                                    echo "<tr>";
                                    echo "<td class='text-center'><span class='row-number'>" . $a . "</span></td>";
                                    echo "<td class='text-center'><span class='date-badge'><i class='fas fa-calendar-alt'></i> " . $date . "</span></td>";
                                    echo "<td>";
                                    echo "<div class='project-name'>";
                                    echo "<div class='project-icon'><i class='fas fa-camera-retro'></i></div>";
                                    echo "<div>";
                                    echo "<div class='project-text'>" . $aname . "</div>";
                                    echo "<div class='project-id'>รหัสโครงการ #" . $aid . "</div>";
                                    echo "</div></div>";
                                    echo "</td>";
                                    echo "<td><div class='action-group'>" . $btn_in . $btn_del . "</div></td>";
                                    echo "<td class='text-center'><div class='status-wrap'>" . $status_m . $status_a . "</div></td>";
                                    echo "<td><div class='action-group'>" . $btn_pm . $btn_pa . "</div></td>";
                                    echo "</tr>";
                                }

                                if ($a == 0) {
                                    echo "<tr class='empty-row'><td colspan='6'>";
                                    echo "<div class='empty-state'>";
                                    echo "<i class='fas fa-camera-retro'></i>";
                                    echo "<h6 class='fw-semibold mb-1'>ยังไม่มีโครงการ/กิจกรรม</h6>";
                                    echo "<p class='small mb-0'>กรุณาเพิ่มโครงการในระบบก่อนเพื่อบันทึกภาพ</p>";
                                    echo "</div></td></tr>";
                                }
                            } catch (PDOException $e) {
                                error_log("reporting_main.php Error: " . $e->getMessage());
                                echo "<tr><td colspan='6' class='text-center text-danger py-4'>";
                                echo "<i class='fas fa-exclamation-triangle me-2'></i>";
                                echo "เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง";
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>
                            <i class="fas fa-info-circle me-1" style="color: #06b6d4;"></i>
                            <span class="me-2"><i class="fas fa-box" style="color: var(--success);"></i> = ภาพวัสดุ</span>
                            <span><i class="fas fa-image" style="color: var(--success);"></i> = ภาพกิจกรรม</span>
                        </span>
                        <span class="badge px-3 py-2 rounded-pill"
                              style="background: #ecfeff; color: #06b6d4;">
                            <i class="fas fa-images me-1"></i>
                            <?php echo number_format($totalWithPhoto); ?> / <?php echo number_format($totalActivities); ?> โครงการมีภาพ
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script>
    function CheckDel() {
        if (confirm('กรุณายืนยันการลบอีกครั้ง !!!')) {
            return true;
        } else {
            return false;
        }
    }

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