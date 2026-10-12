<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php
        include "./modules/head.inc.php";
        include "./includes/connect.php";
        include "./includes/function.php";
        
        $act_id = filter_input(INPUT_GET, 'act_id', FILTER_VALIDATE_INT);
        
        if ($act_id === false || $act_id === null) {
            echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
            exit;
        }
        
        $sql = "SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
        
        if ($stmt->rowCount() == 0) {
            echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
            exit;
        }
    ?>
    <title>รายละเอียดโครงการ | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
        .show-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .show-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .show-hero::after {
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
            flex-shrink: 0;
        }
        .hero-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }
        .hero-subtitle {
            font-size: 0.925rem;
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
            padding: 0.7rem 1.4rem;
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

        /* ===== Stat Cards ===== */
        .stat-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.25rem;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            transition: height 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        .stat-card:hover::before { height: 6px; }

        .stat-blue::before   { background: linear-gradient(90deg, #4f46e5, #4338ca); }
        .stat-cyan::before   { background: linear-gradient(90deg, #06b6d4, #0891b2); }
        .stat-pink::before   { background: linear-gradient(90deg, #ec4899, #db2777); }
        .stat-orange::before { background: linear-gradient(90deg, #f97316, #ea580c); }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .stat-card:hover .stat-icon {
            transform: scale(1.08) rotate(-5deg);
        }
        .icon-blue   { background: linear-gradient(135deg, #4f46e5, #4338ca); }
        .icon-cyan   { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .icon-pink   { background: linear-gradient(135deg, #ec4899, #db2777); }
        .icon-orange { background: linear-gradient(135deg, #f97316, #ea580c); }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.825rem;
            margin: 0;
            font-weight: 500;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
            margin: 0.15rem 0 0 0;
            letter-spacing: -1px;
        }
        .value-blue   { color: #4338ca; }
        .value-cyan   { color: #0891b2; }
        .value-pink   { color: #db2777; }

        .stat-action-btn {
            background: linear-gradient(135deg, #f97316, #ec4899);
            color: white;
            border: none;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.25);
        }
        .stat-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
            color: white;
            filter: brightness(1.05);
        }

        /* ===== Modern Cards ===== */
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
        .modern-card.card-green::before {
            background: linear-gradient(90deg, #10b981, #059669);
        }
        .modern-card:hover { box-shadow: var(--shadow-md); }

        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-soft);
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .modern-card .card-header h5 {
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
        .card-header-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .modern-card .card-body { padding: 0; }

        /* ===== Info Table ===== */
        .info-table {
            margin: 0;
            border-collapse: collapse;
            width: 100%;
        }
        .info-table td {
            padding: 0.85rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-soft);
            font-size: 0.925rem;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td:first-child {
            width: 180px;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg-soft);
            white-space: nowrap;
        }
        .info-table td:first-child i {
            color: var(--primary);
            margin-right: 0.5rem;
            width: 18px;
            text-align: center;
        }
        .info-table td:not(:first-child) {
            color: var(--text-dark);
        }
        .info-table tr:hover td:not(:first-child) {
            background: rgba(79, 70, 229, 0.02);
        }
        .info-highlight {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 1rem;
        }
        .info-pill {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-map {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }
        .btn-map:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(79,70,229,0.25);
        }

        /* ===== Add Button ===== */
        .btn-add-modern {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.5rem 1.1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-add-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
            color: white;
            filter: brightness(1.05);
        }

        /* ===== Student Table ===== */
        .student-table-wrap {
            padding: 1.25rem;
        }
        #studentTable {
            width: 100% !important;
        }
        #studentTable thead th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: #fff !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 0.85rem 0.75rem !important;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        #studentTable thead th:first-child { border-top-left-radius: var(--radius-sm); }
        #studentTable thead th:last-child  { border-top-right-radius: var(--radius-sm); }
        #studentTable tbody td {
            padding: 0.75rem !important;
            vertical-align: middle !important;
            border-color: var(--border-soft) !important;
            font-size: 0.925rem;
        }
        #studentTable tbody tr {
            transition: all 0.2s ease;
        }
        #studentTable tbody tr:hover {
            background: var(--primary-light) !important;
        }
        .gender-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-male {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: white;
        }
        .badge-female {
            background: linear-gradient(135deg, #ec4899, #db2777);
            color: white;
        }
        .age-pill {
            display: inline-block;
            background: var(--bg-soft);
            color: var(--text-dark);
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid var(--border-soft);
        }
        .btn-delete {
            background: white;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 0.4rem 0.7rem;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-edit {
            background: white;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            padding: 0.4rem 0.7rem;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
        .btn-delete:hover {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        /* DataTables custom */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-soft);
            padding: 0.4rem 0.75rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            outline: none;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary-dark) !important;
            border-color: var(--primary) !important;
        }

        .card-footer-modern {
            background: var(--bg-soft);
            border-top: 1px solid var(--border-soft);
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card, .modern-card { animation: fadeInUp 0.4s ease-out backwards; }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }

        /* Responsive */
        @media (max-width: 768px) {
            .show-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.2rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .info-table td:first-child { width: 130px; font-size: 0.85rem; }
            .stat-value { font-size: 1.5rem; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            // Fetch activity details
            $sql = "SELECT activity.activity_id, activity.activity_name, 
                    course.course_name, course.course_hour, 
                    culture.culture_name, format.format_name_show, 
                    attribute.attribute_name, type.type_name, 
                    lecturer.firstname, lecturer.lastname, 
                    activity.place, activity.address, 
                    activity.latitude, activity.longtitude, 
                    activity.date_id, activity.date_range, 
                    activity.date_start, activity.date_end, 
                    activity.time_start, activity.time_end, 
                    activity.total_study 
                    FROM activity 
                    LEFT JOIN attribute ON activity.attribute_id = attribute.attribute_id
                    LEFT JOIN course ON activity.course_id = course.course_id
                    LEFT JOIN format ON activity.format_id = format.format_id
                    LEFT JOIN lecturer ON activity.lecturer_id = lecturer.lecturer_id
                    LEFT JOIN culture ON culture.culture_id = course.culture_id
                    LEFT JOIN type ON activity.type_id = type.type_id
                    WHERE activity.activity_id = :act_id AND activity.school_id = :school_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
            $row = $stmt->fetch();

            $activity_id = $row['activity_id'];
            $activity_name = $row['activity_name'];
            $course_name = $row['course_name'];
            $course_hour = $row['course_hour'];
            $culture_name = $row['culture_name'];
            $format_name_show = $row['format_name_show'];
            $attribute_name = $row['attribute_name'];
            $type_name = $row['type_name'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $place = $row['place'];
            $address = $row['address'];
            $latitude = $row['latitude'];
            $longtitude = $row['longtitude'];
            $time_start = $row['time_start'];
            $time_end = $row['time_end'];
            $total_study = $row['total_study'];

            if ($row['date_id'] == "1") {
                if ($row['date_start'] == $row['date_end']) {
                    $date = ADYearToBEYearTH($row['date_start'], 2);
                } else {
                    $date = ADYearToBEYearTH($row['date_start'], 2) . " ถึง " . ADYearToBEYearTH($row['date_end'], 2);
                }
            } else {
                $date = DateRangeToString($row['date_range'], 2);
            }

            // Fetch gender breakdown
            $sql_count = "SELECT 
                COUNT(CASE WHEN st.sex_id = 1 THEN 1 END) as men_study,
                COUNT(CASE WHEN st.sex_id = 2 THEN 1 END) as women_study
            FROM train tr
            INNER JOIN student st ON tr.student_id = st.student_id
            WHERE tr.activity_id = :act_id AND st.school_id = :school_id";
            $stmt_count = $pdo->prepare($sql_count);
            $stmt_count->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
            $row_count = $stmt_count->fetch();
            $men_study = $row_count['men_study'];
            $women_study = $row_count['women_study'];
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="act_main.php">ข้อมูลกิจกรรม/โครงการ</a></li>
                    <li class="breadcrumb-item active fw-semibold">รายละเอียดโครงการ</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="show-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-info-circle"></i> รายละเอียดโครงการ
                            </span>
                            <h1 class="hero-title mt-2"><?= htmlspecialchars($activity_name) ?></h1>
                            <p class="hero-subtitle">
                                <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($course_name) ?> 
                                · <i class="fas fa-clock me-1"></i> <?= $course_hour ?> ชั่วโมง
                            </p>
                        </div>
                    </div>
                    <a href="act_main.php" class="btn-hero-back">
                        <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                    </a>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-blue">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon icon-blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="stat-label">ผู้เรียนทั้งหมด</p>
                                <div class="stat-value value-blue"><?= number_format($total_study) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-cyan">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon icon-cyan">
                                <i class="fas fa-male"></i>
                            </div>
                            <div>
                                <p class="stat-label">ผู้เรียนเพศชาย</p>
                                <div class="stat-value value-cyan"><?= number_format($men_study) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-pink">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon icon-pink">
                                <i class="fas fa-female"></i>
                            </div>
                            <div>
                                <p class="stat-label">ผู้เรียนเพศหญิง</p>
                                <div class="stat-value value-pink"><?= number_format($women_study) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-orange d-flex align-items-center justify-content-center">
                        <a href="train_in.php?act_id=<?= $activity_id ?>" class="stat-action-btn">
                            <i class="fas fa-user-plus"></i> จัดการผู้เรียน
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Details Card -->
            <div class="modern-card">
                <div class="card-header">
                    <h5>
                        <span class="card-header-icon"><i class="fas fa-info-circle"></i></span>
                        รายละเอียดข้อมูลโครงการ
                    </h5>
                </div>
                <div class="card-body">
                    <table class="info-table">
                        <tbody>
                            <tr>
                                <td><i class="fas fa-archive"></i> ชื่อโครงการ</td>
                                <td><span class="info-highlight"><?= htmlspecialchars($activity_name) ?></span></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-graduation-cap"></i> หลักสูตร</td>
                                <td><?= htmlspecialchars($course_name) ?> <span class="info-pill"><?= $course_hour ?> ชั่วโมง</span></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-layer-group"></i> กลุ่มอาชีพ</td>
                                <td><span class="info-pill"><?= htmlspecialchars($culture_name) ?></span></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-shapes"></i> รูปแบบการจัด</td>
                                <td><?= htmlspecialchars($format_name_show) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-tag"></i> ลักษณะโครงการ</td>
                                <td><?= htmlspecialchars($attribute_name) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-list-ul"></i> ประเภทโครงการ</td>
                                <td><?= htmlspecialchars($type_name) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user-tie"></i> วิทยากร</td>
                                <td><strong><?= htmlspecialchars($firstname . ' ' . $lastname) ?></strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-map-marker-alt"></i> สถานที่จัด</td>
                                <td><?= htmlspecialchars($place) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-map"></i> ที่อยู่</td>
                                <td><?= htmlspecialchars($address) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-globe"></i> พิกัด GPS</td>
                                <td>
                                    <code><?= $latitude ?>, <?= $longtitude ?></code>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $latitude ?>,<?= $longtitude ?>" target="_blank" class="btn-map">
                                        <i class="fas fa-map-marker-alt"></i> เปิดแผนที่
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="far fa-calendar-alt"></i> วันที่ดำเนินการ</td>
                                <td><span class="info-highlight"><?= htmlspecialchars($date) ?></span></td>
                            </tr>
                            <tr>
                                <td><i class="far fa-clock"></i> เวลาดำเนินการ</td>
                                <td>
                                    <span class="info-pill"><?= htmlspecialchars($time_start) ?> น.</span>
                                    <span class="text-muted mx-1">ถึง</span>
                                    <span class="info-pill"><?= htmlspecialchars($time_end) ?> น.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Student List Card -->
            <div class="modern-card card-green">
                <div class="card-header">
                    <h5>
                        <span class="card-header-icon green"><i class="fas fa-users"></i></span>
                        รายชื่อผู้เรียนที่เข้าร่วม
                    </h5>
                    <a href="train_in.php?act_id=<?= $activity_id ?>" class="btn-add-modern">
                        <i class="fas fa-plus"></i> เพิ่มผู้เรียน
                    </a>
                </div>
                <div class="card-body">
                    <div class="student-table-wrap">
                        <div class="table-responsive">
                            <table id="studentTable" class="table align-middle mb-0" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;" class="text-center">ลำดับ</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th style="width: 110px;" class="text-center">เพศ</th>
                                        <th style="width: 90px;" class="text-center">อายุ</th>
                                        <th style="width: 90px;" class="text-center">แก้ไข</th>
                                        <th style="width: 90px;" class="text-center">ลบออก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $a = 0;
                                    $sql = "SELECT student.student_id, student.firstname, 
                                            student.lastname, sex.sex_name, 
                                            student.birthday 
                                            FROM train 
                                            INNER JOIN student ON train.student_id = student.student_id 
                                            INNER JOIN sex ON sex.sex_id = student.sex_id 
                                            WHERE train.activity_id = :act_id AND student.school_id = :school_id 
                                            ORDER BY student.student_id DESC";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);

                                    while ($row = $stmt->fetch()) {
                                        $a++;
                                        $age = BirthDateAge($row['birthday']);
                                        $genderClass = ($row['sex_name'] == 'ชาย') ? 'badge-male' : 'badge-female';
                                        $genderIcon  = ($row['sex_name'] == 'ชาย') ? 'fa-male' : 'fa-female';
                                        echo "<tr>";
                                        echo "<td class='text-center fw-bold' style='color:#4f46e5'>$a</td>";
                                        echo "<td><i class='fas fa-user-circle me-2' style='color:#94a3b8'></i><strong>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</strong></td>";
                                        echo "<td class='text-center'><span class='gender-badge $genderClass'><i class='fas $genderIcon me-1'></i>" . htmlspecialchars($row['sex_name']) . "</span></td>";
                                        echo "<td class='text-center'><span class='age-pill'>$age ปี</span></td>";
                                        echo "<td class='text-center'>";
                                        echo "<a href='student_in.php?submit=EDIT&edit_id={$row['student_id']}&act_id=$act_id' class='btn-edit' title='แก้ไขข้อมูลผู้เรียน'>";
                                        echo "<i class='fas fa-pen'></i>";
                                        echo "</a>";
                                        echo "</td>";
                                        echo "<td class='text-center'>";
                                        echo "<a href='train_del.php?act_id=$act_id&del_id={$row['student_id']}' onclick='return CheckDel()' class='btn-delete' title='ลบออกจากหลักสูตร'>";
                                        echo "<i class='fas fa-trash'></i>";
                                        echo "</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer-modern">
                    <i class="fas fa-info-circle me-1"></i> สามารถค้นหาและเรียงลำดับได้จากตาราง
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    function CheckDel() {
        return confirm('กรุณายืนยันการลบอีกครั้ง !!!');
    }

    $(function () {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#studentTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 15,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                },
                columnDefs: [
                    { targets: [0, 2, 3, 4, 5], className: 'dt-center' }
                ]
            });
        }
    });
</script>
</body>
</html>
<?php
} 
?>