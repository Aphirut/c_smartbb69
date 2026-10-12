<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php
    include "./modules/head.inc.php";
    include "./includes/connect.php";
    include "./includes/course_groups.php";
    ?>
    <title>รายการหลักสูตร | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
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
        .course-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .course-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .course-hero::after {
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
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.6rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-hero-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateX(-3px);
        }

        /* ===== Stats Cards ===== */
        .stats-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.25rem;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }
        .stats-card.total::before  { background: linear-gradient(90deg, #4f46e5, #7c3aed); }
        .stats-card.hours::before  { background: linear-gradient(90deg, #06b6d4, #0891b2); }
        .stats-card.school::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .stats-card.files::before  { background: linear-gradient(90deg, #10b981, #059669); }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            margin-bottom: 0.85rem;
        }
        .stats-card.total .stats-icon  { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stats-card.hours .stats-icon  { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .stats-card.school .stats-icon { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .stats-card.files .stats-icon  { background: linear-gradient(135deg, #10b981, #059669); }

        .stats-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .stats-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .stats-unit {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== Main Table Card ===== */
        .table-card {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }
        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to right, #fafbff, white);
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
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
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
        .count-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* ===== DataTable ===== */
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
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .modern-table tbody td {
            padding: 0.9rem 1.25rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle;
            font-size: 0.92rem;
        }
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        .modern-table tbody tr:hover {
            background-color: #fafbff !important;
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Course Icon */
        .course-icon-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .course-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: linear-gradient(135deg, #eef2ff, #ede9fe);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .course-name {
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.3;
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

        /* Hour Badge */
        .hour-badge {
            background: linear-gradient(135deg, #ecfeff, #cffafe);
            color: #0891b2;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border: 1px solid #a5f3fc;
        }

        /* School Badge */
        .school-cell {
            color: var(--text-dark);
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .school-cell i {
            color: var(--warning);
        }

        /* PDF Link */
        .pdf-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-radius: 11px;
            transition: all 0.25s ease;
            color: #dc2626;
            text-decoration: none;
            border: 1px solid #fecaca;
        }
        .pdf-link:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 4px 12px rgba(220,38,38,0.3);
            border-color: transparent;
        }
        .no-file {
            color: var(--text-muted);
            font-size: 1.25rem;
        }

        /* Action button */
        .action-btn-modern {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .action-btn-delete {
            background: #fef2f2;
            color: var(--danger);
        }
        .action-btn-delete:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
        }

        /* DataTables Custom */
        .dataTables_wrapper { padding: 1.25rem 1.5rem; }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.5rem 0.9rem 0.5rem 2.25rem;
            font-size: 0.9rem;
            background: var(--bg-soft) url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 0.65rem center;
            min-width: 240px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
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
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(79,70,229,0.3);
        }

        /* Empty State */
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

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stats-card, .table-card { animation: fadeInUp 0.4s ease-out; }

        /* Responsive */
        @media (max-width: 768px) {
            .course-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .stats-value { font-size: 1.5rem; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <?php
    $culture_id = isset($_REQUEST['culture_id']) ? (int)$_REQUEST['culture_id'] : 0;
    $group_id = isset($_REQUEST['group']) ? (int)$_REQUEST['group'] : 0;

    // โหมดหลักสูตร: ?group=1/2/3 → กรองจาก culture_id ที่ถูกยุบรวมเข้าหลักสูตรนั้น
    $groupCultures = [];
    if ($group_id > 0 && isset($courseGroups[$group_id])) {
        $groupCultures = array_map('intval', $courseGroups[$group_id]['cultures']);
    }
    $is_group = !empty($groupCultures);

    $is_all = ($culture_id <= 0 && !$is_group);
    $culture_name = $is_all ? "หลักสูตรทั้งหมด" : "ไม่พบข้อมูล";
    $course_count = 0;
    $total_hours = 0;
    $avg_hours = 0;
    $school_count = 0;
    $file_count = 0;

    try {
        if ($is_group) {
            // ===== โหมดหลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง =====
            $in = implode(',', array_fill(0, count($groupCultures), '?'));
            $culture_name = $courseGroups[$group_id]['title'];

            // จำนวนหลักสูตร
            $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM course WHERE culture_id IN ($in)");
            $stmt2->execute($groupCultures);
            $course_count = $stmt2->fetchColumn();

            // ชั่วโมงรวม/เฉลี่ย
            $stmt3 = $pdo->prepare("SELECT SUM(course_hour) AS total, AVG(course_hour) AS avg FROM course WHERE culture_id IN ($in)");
            $stmt3->execute($groupCultures);
            $hourData = $stmt3->fetch(PDO::FETCH_ASSOC);
            $total_hours = (int)($hourData['total'] ?? 0);
            $avg_hours = round((float)($hourData['avg'] ?? 0), 1);

            // จำนวน กศน. อำเภอ
            $stmt4 = $pdo->prepare("SELECT COUNT(DISTINCT school_id) FROM course WHERE culture_id IN ($in)");
            $stmt4->execute($groupCultures);
            $school_count = $stmt4->fetchColumn();

            // จำนวนไฟล์ PDF
            $stmt5 = $pdo->prepare("SELECT COUNT(*) FROM course WHERE culture_id IN ($in) AND course_file IS NOT NULL AND course_file != ''");
            $stmt5->execute($groupCultures);
            $file_count = $stmt5->fetchColumn();
        } elseif ($is_all) {
            // สถิติรวมทั้งหมด
            $stmt = $pdo->query("SELECT COUNT(*) FROM course");
            $course_count = $stmt->fetchColumn();

            $stmt3 = $pdo->query("SELECT SUM(course_hour) AS total, AVG(course_hour) AS avg FROM course");
            $hourData = $stmt3->fetch(PDO::FETCH_ASSOC);
            $total_hours = (int)($hourData['total'] ?? 0);
            $avg_hours = round((float)($hourData['avg'] ?? 0), 1);

            $stmt4 = $pdo->query("SELECT COUNT(DISTINCT school_id) FROM course");
            $school_count = $stmt4->fetchColumn();

            $stmt5 = $pdo->query("SELECT COUNT(*) FROM course WHERE course_file IS NOT NULL AND course_file != ''");
            $file_count = $stmt5->fetchColumn();
        } else {
            $stmt = $pdo->prepare("SELECT culture_name FROM culture WHERE culture_id = :culture_id LIMIT 1");
            $stmt->execute(['culture_id' => $culture_id]);
            $culture = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($culture) {
                $culture_name = $culture['culture_name'];
            }

            // จำนวนหลักสูตร
            $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM course WHERE culture_id = :culture_id");
            $stmt2->execute(['culture_id' => $culture_id]);
            $course_count = $stmt2->fetchColumn();

            // ชั่วโมงรวม/เฉลี่ย
            $stmt3 = $pdo->prepare("SELECT SUM(course_hour) AS total, AVG(course_hour) AS avg FROM course WHERE culture_id = :culture_id");
            $stmt3->execute(['culture_id' => $culture_id]);
            $hourData = $stmt3->fetch(PDO::FETCH_ASSOC);
            $total_hours = (int)($hourData['total'] ?? 0);
            $avg_hours = round((float)($hourData['avg'] ?? 0), 1);

            // จำนวน กศน. อำเภอ
            $stmt4 = $pdo->prepare("SELECT COUNT(DISTINCT school_id) FROM course WHERE culture_id = :culture_id");
            $stmt4->execute(['culture_id' => $culture_id]);
            $school_count = $stmt4->fetchColumn();

            // จำนวนไฟล์ PDF
            $stmt5 = $pdo->prepare("SELECT COUNT(*) FROM course WHERE culture_id = :culture_id AND course_file IS NOT NULL AND course_file != ''");
            $stmt5->execute(['culture_id' => $culture_id]);
            $file_count = $stmt5->fetchColumn();
        }
    } catch (PDOException $e) {
        $culture_name = "เกิดข้อผิดพลาด";
    }
    ?>

    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="./course_main.php">หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= htmlspecialchars($culture_name) ?></li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="course-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-layer-group"></i>
                                <?= $is_group ? "หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง" : "ระบบจัดการหลักสูตร" ?>
                            </span>
                            <h1 class="hero-title mt-2"><?= htmlspecialchars($culture_name) ?></h1>
                            <p class="hero-subtitle"><?= $is_all ? "รายการหลักสูตรทั้งหมดในระบบ (ทุกหลักสูตร) พร้อมเอกสารประกอบ" : "รายการหลักสูตรทั้งหมด พร้อมเอกสารประกอบ" ?></p>
                        </div>
                    </div>
                    <a href="./course_main.php" class="btn-hero-back">
                        <i class="fas fa-arrow-left me-1"></i> กลับไปเลือกหลักสูตร
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card total">
                        <div class="stats-icon"><i class="fas fa-book"></i></div>
                        <div class="stats-label"><?= $is_all ? "หลักสูตรทั้งหมด" : "หลักสูตรในหมวดนี้" ?></div>
                        <div class="stats-value"><?= number_format($course_count) ?></div>
                        <div class="stats-unit">หลักสูตร</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card hours">
                        <div class="stats-icon"><i class="fas fa-clock"></i></div>
                        <div class="stats-label">ชั่วโมงเฉลี่ย</div>
                        <div class="stats-value"><?= number_format($avg_hours, 1) ?></div>
                        <div class="stats-unit">ชั่วโมง/หลักสูตร</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card school">
                        <div class="stats-icon"><i class="fas fa-school"></i></div>
                        <div class="stats-label">กศน. อำเภอ</div>
                        <div class="stats-value"><?= number_format($school_count) ?></div>
                        <div class="stats-unit">แห่งที่เปิดสอน</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card files">
                        <div class="stats-icon"><i class="fas fa-file-pdf"></i></div>
                        <div class="stats-label">มีไฟล์เอกสาร</div>
                        <div class="stats-value"><?= number_format($file_count) ?></div>
                        <div class="stats-unit">จาก <?= number_format($course_count) ?> หลักสูตร</div>
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
                            <h6>รายการหลักสูตร</h6>
                            <p>คลิกที่ไฟล์ PDF เพื่อดูเนื้อหาหลักสูตร</p>
                        </div>
                    </div>
                    <span class="count-badge">
                        <i class="fas fa-database"></i> <?= number_format($course_count) ?> หลักสูตร
                    </span>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table modern-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;" class="text-center">ลำดับ</th>
                                <th>ชื่อหลักสูตร</th>
                                <th style="width: 18%;">กลุ่มอาชีพ</th>
                                <th style="width: 110px;" class="text-center">ชั่วโมง</th>
                                <th style="width: 24%;">กศน.อำเภอ</th>
                                <th style="width: 90px;" class="text-center">ไฟล์</th>
                                <?php if (isset($_SESSION['ss_school_id']) && $_SESSION['ss_login_status'] == 1): ?>
                                    <th style="width: 100px;" class="text-center">ดำเนินการ</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $where = "";
                                $params = [];
                                if ($is_group) {
                                    $in = implode(',', array_fill(0, count($groupCultures), '?'));
                                    $where = " WHERE course.culture_id IN ($in) ";
                                    $params = $groupCultures;
                                } elseif ($culture_id > 0) {
                                    $where = " WHERE course.culture_id = :culture_id ";
                                    $params = ['culture_id' => $culture_id];
                                }
                                $sql = "SELECT course.course_id, course.course_name, 
                                        course.culture_id, course.course_hour, 
                                        course.course_file, school.school_name,
                                        culture.culture_name 
                                        FROM course 
                                        INNER JOIN school ON course.school_id = school.school_id 
                                        LEFT JOIN culture ON course.culture_id = culture.culture_id 
                                        $where 
                                        ORDER BY course.course_name ASC, course.course_hour ASC";

                                $stmt = $pdo->prepare($sql);
                                $stmt->execute($params);

                                $a = 0;
                                while ($row = $stmt->fetch()) {
                                    $a++;
                                    $courseName = htmlspecialchars($row['course_name']);
                                    $schoolName = htmlspecialchars($row['school_name']);
                                    $hour = htmlspecialchars($row['course_hour']);
                            ?>
                                <tr>
                                    <td class="text-center"><span class="row-number"><?= $a ?></span></td>
                                    <td>
                                        <div class="course-icon-cell">
                                            <div class="course-icon-box">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="course-name"><?= $courseName ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="school-cell"><i class="fas fa-layer-group"></i> <?= htmlspecialchars($row['culture_name'] ?? '') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="hour-badge">
                                            <i class="fas fa-clock"></i> <?= $hour ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="school-cell">
                                            <i class="fas fa-school"></i> <?= $schoolName ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($row['course_file'])): ?>
                                            <a href="course_show_pdf.php?file=<?= urlencode($row['course_file']) ?>"
                                               target="_blank"
                                               class="pdf-link"
                                               data-bs-toggle="tooltip" title="ดูไฟล์ PDF">
                                                <i class="fas fa-file-pdf fa-lg"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="no-file"><i class="fas fa-minus"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (isset($_SESSION['ss_school_id']) && $_SESSION['ss_login_status'] == 1): ?>
                                        <td class="text-center">
                                            <a href="course_del.php?submit=DEL&del_id=<?= $row['course_id'] ?>&culture_id=<?= $row['culture_id'] ?>"
                                               onclick="return CheckDel()"
                                               class="action-btn-modern action-btn-delete"
                                               data-bs-toggle="tooltip" title="ลบหลักสูตร">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php
                                }
                                if ($a === 0) {
                                    $hasActionCol = (isset($_SESSION['ss_school_id']) && $_SESSION['ss_login_status'] == 1);
                                    $colCount = $hasActionCol ? 7 : 6;
                                    $emptyTitle = $is_all ? 'ไม่พบหลักสูตร' : 'ไม่พบหลักสูตรในหมวดนี้';
                                    $emptyDesc = $is_all ? 'ยังไม่มีข้อมูลหลักสูตรในระบบ หรือลองเพิ่มหลักสูตรใหม่ผ่านเมนูจัดการ' : 'ลองเลือกหลักสูตรอื่นหรือเพิ่มหลักสูตรใหม่';
                                    echo '<tr><td colspan="'.$colCount.'" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h6 class="fw-semibold mb-1">' . $emptyTitle . '</h6>
                                                <p class="small mb-3">' . $emptyDesc . '</p>
                                                <a href="./course_main.php" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-arrow-left me-1"></i> กลับไปเลือกกลุ่ม
                                                </a>
                                            </div>
                                          </td></tr>';
                                }
                            } catch (PDOException $e) {
                                error_log("course_show.php Error: " . $e->getMessage());
                                $colspan = (isset($_SESSION['ss_school_id']) && $_SESSION['ss_login_status'] == 1) ? 7 : 6;
                                echo '<tr><td colspan="'.$colspan.'" class="text-center text-danger py-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
                                      </td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>
                            <i class="fas fa-info-circle text-primary me-1"></i>
                            คลิกที่หัวตารางเพื่อเรียงลำดับ • ใช้ช่องค้นหาเพื่อค้นหาหลักสูตร
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-clock me-1"></i>
                            รวม <?= number_format($total_hours) ?> ชั่วโมง
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    function CheckDel() {
        return confirm('⚠️ กรุณายืนยันการลบข้อมูลอีกครั้ง !!!\n\nการลบจะไม่สามารถย้อนกลับได้');
    }

    $(function () {
        // เปิดใช้งาน Tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

        if (typeof $.fn.DataTable !== 'undefined') {
            var hasData = $('#example1 tbody tr').length > 0 && !$('#example1 tbody tr').first().find('td[colspan]').length;

            if (hasData) {
                $('#example1').DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 20,
                    lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "ทั้งหมด"]],
                    order: [[1, 'asc']],
                    language: {
                        search: "",
                        searchPlaceholder: "🔍 ค้นหาหลักสูตร...",
                        lengthMenu: "แสดง _MENU_ รายการ",
                        info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
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
                        { targets: [0, 2, 4], className: 'dt-center' },
                        { targets: [0, 4, 5], orderable: false }
                    ]
                });
            }
        }
    });
</script>
</body>
</html>