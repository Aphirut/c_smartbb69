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
    ?>
    <title>ข้อมูลผู้เรียน | C-Smart</title>

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
        .student-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .student-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .student-hero::after {
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
        .btn-hero-add {
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
        .btn-hero-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }
        .btn-add-modern {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 4px 12px rgba(79,70,229,0.3);
        }
        .btn-add-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79,70,229,0.4);
            color: white;
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
        .stats-card.total::before { background: linear-gradient(90deg, #4f46e5, #7c3aed); }
        .stats-card.male::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
        .stats-card.female::before { background: linear-gradient(90deg, #ec4899, #db2777); }
        .stats-card.recent::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }

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
        .stats-card.total .stats-icon { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stats-card.male .stats-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .stats-card.female .stats-icon { background: linear-gradient(135deg, #ec4899, #db2777); }
        .stats-card.recent .stats-icon { background: linear-gradient(135deg, #f59e0b, #ef4444); }

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
            transform: scale(1.001);
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Student Avatar */
        .student-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            margin-right: 0.65rem;
            flex-shrink: 0;
        }
        .avatar-1 { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .avatar-2 { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .avatar-3 { background: linear-gradient(135deg, #ec4899, #db2777); }
        .avatar-4 { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .avatar-5 { background: linear-gradient(135deg, #10b981, #059669); }

        .student-name-cell {
            display: flex;
            align-items: center;
        }
        .student-name {
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.2;
        }
        .student-name-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ID Card Badge */
        .id-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            font-family: 'Sarabun', monospace;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-block;
        }

        /* Phone Badge */
        .phone-badge {
            background: #ecfdf5;
            color: #059669;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
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

        /* Action Buttons */
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
        .action-btn-edit {
            background: #eef2ff;
            color: var(--primary);
        }
        .action-btn-edit:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
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
        .action-btn-view {
            background: #ecfeff;
            color: var(--info);
        }
        .action-btn-view:hover {
            background: var(--info);
            color: white;
            transform: translateY(-2px);
        }

        /* DataTables Custom Styling */
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
            transition: all 0.2s ease;
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

        /* Responsive */
        @media (max-width: 768px) {
            .student-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .stats-value { font-size: 1.5rem; }
            .table-card-header { flex-direction: column; align-items: flex-start; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stats-card,
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
            // คำนวณสถิติต่างๆ
            $totalStudents = 0;
            $maleCount = 0;
            $femaleCount = 0;
            $recentCount = 0;
            $school_id = $_SESSION['ss_school_id'];
            try {

                // จำนวนทั้งหมด
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE school_id = :school_id");
                $countStmt->execute(['school_id' => $school_id]);
                $totalStudents = $countStmt->fetchColumn();

                // จำนวนผู้ชาย (sex_id = 1)
                $maleStmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE school_id = :school_id AND sex_id = 1");
                $maleStmt->execute(['school_id' => $school_id]);
                $maleCount = $maleStmt->fetchColumn();

                // จำนวนผู้หญิง (sex_id = 2)
                $femaleStmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE school_id = :school_id AND sex_id = 2");
                $femaleStmt->execute(['school_id' => $school_id]);
                $femaleCount = $femaleStmt->fetchColumn();

                // ลงทะเบียนภายใน 30 วันล่าสุด
                $recentStmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE school_id = :school_id AND register >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                $recentStmt->execute(['school_id' => $school_id]);
                $recentCount = $recentStmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("student_main.php Stats Error: " . $e->getMessage());
            }
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">ข้อมูลผู้เรียน</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="student-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-database"></i>
                                ระบบบริหารจัดการข้อมูล
                            </span>
                            <h1 class="hero-title mt-2">ข้อมูลผู้เรียน</h1>
                            <p class="hero-subtitle">จัดการรายชื่อ ค้นหา และแก้ไขข้อมูลผู้เรียนในระบบ C-Smart</p>
                        </div>
                    </div>
                    <a href="student_in.php" class="btn-hero-add">
                        <i class="fas fa-plus-circle"></i> เพิ่มผู้เรียนใหม่
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card total">
                        <div class="stats-icon"><i class="fas fa-users"></i></div>
                        <div class="stats-label">ผู้เรียนทั้งหมด</div>
                        <div class="stats-value"><?= number_format($totalStudents) ?></div>
                        <div class="stats-unit">คน</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card male">
                        <div class="stats-icon"><i class="fas fa-mars"></i></div>
                        <div class="stats-label">ผู้เรียนชาย</div>
                        <div class="stats-value"><?= number_format($maleCount) ?></div>
                        <div class="stats-unit">คน (<?= $totalStudents > 0 ? round(($maleCount/$totalStudents)*100, 1) : 0 ?>%)</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card female">
                        <div class="stats-icon"><i class="fas fa-venus"></i></div>
                        <div class="stats-label">ผู้เรียนหญิง</div>
                        <div class="stats-value"><?= number_format($femaleCount) ?></div>
                        <div class="stats-unit">คน (<?= $totalStudents > 0 ? round(($femaleCount/$totalStudents)*100, 1) : 0 ?>%)</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card recent">
                        <div class="stats-icon"><i class="fas fa-user-clock"></i></div>
                        <div class="stats-label">สมัครใหม่ (30 วัน)</div>
                        <div class="stats-value"><?= number_format($recentCount) ?></div>
                        <div class="stats-unit">คน</div>
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
                            <h6>รายชื่อผู้เรียน</h6>
                            <p>คลิกที่หัวตารางเพื่อเรียงลำดับ และใช้ช่องค้นหาเพื่อค้นหา</p>
                        </div>
                    </div>
                    <a href="student_in.php" class="btn-add-modern">
                        <i class="fas fa-plus"></i> เพิ่มผู้เรียน
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table modern-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 70px;" class="text-center">ลำดับ</th>
                                <th style="width: 160px;" class="text-center">บัตรประชาชน</th>
                                <th>ชื่อ-สกุล</th>
                                <th>ที่อยู่</th>
                                <th style="width: 140px;" class="text-center">โทรศัพท์</th>
                                <th style="width: 140px;" class="text-center">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $a = 0;
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM student WHERE school_id = :school_id ORDER BY student_id DESC");
                                $stmt->execute(['school_id' => $school_id]);

                                while ($row = $stmt->fetch()) {
                                    $a++;
                                    $firstChar = mb_substr($row['firstname'], 0, 1, 'UTF-8');
                                    $avatarClass = 'avatar-' . (($a % 5) + 1);
                                    $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                                    $address = htmlspecialchars($row['address']);
                                    $idcard = htmlspecialchars($row['idcard']);
                                    $tel = htmlspecialchars($row['tel']);

                                    echo "<tr>";
                                    echo "<td class='text-center'><span class='row-number'>{$a}</span></td>";
                                    echo "<td class='text-center'><span class='id-badge'>{$idcard}</span></td>";
                                    echo "<td>
                                            <div class='student-name-cell'>
                                                <div class='student-avatar {$avatarClass}'>" . htmlspecialchars($firstChar) . "</div>
                                                <div>
                                                    <div class='student-name'>{$fullname}</div>
                                                    <div class='student-name-sub'>รหัส #{$row['student_id']}</div>
                                                </div>
                                            </div>
                                          </td>";
                                    echo "<td class='text-muted small'><i class='fas fa-map-marker-alt text-warning me-1'></i>{$address}</td>";
                                    echo "<td class='text-center'><span class='phone-badge'><i class='fas fa-phone'></i>{$tel}</span></td>";
                                    echo "<td class='text-center'>
                                            <div class='d-inline-flex gap-1'>
                                                <a href='student_in.php?submit=EDIT&edit_id={$row['student_id']}' 
                                                   class='action-btn-modern action-btn-edit' 
                                                   data-bs-toggle='tooltip' title='แก้ไขข้อมูล'>
                                                   <i class='fas fa-edit'></i>
                                                </a>
                                                <a href='student_del.php?submit=DEL&del_id={$row['student_id']}' 
                                                   onclick='return CheckDel()' 
                                                   class='action-btn-modern action-btn-delete' 
                                                   data-bs-toggle='tooltip' title='ลบข้อมูล'>
                                                   <i class='fas fa-trash'></i>
                                                </a>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                }

                                if ($a == 0) {
                                    echo "<tr><td colspan='6'>
                                            <div class='empty-state'>
                                                <i class='fas fa-inbox'></i>
                                                <h6 class='fw-semibold mb-1'>ยังไม่มีข้อมูลผู้เรียน</h6>
                                                <p class='small mb-3'>เริ่มต้นด้วยการเพิ่มผู้เรียนคนแรกของคุณ</p>
                                                <a href='student_in.php' class='btn btn-primary btn-sm'>
                                                    <i class='fas fa-plus me-1'></i> เพิ่มผู้เรียน
                                                </a>
                                            </div>
                                          </td></tr>";
                                }
                            } catch (PDOException $e) {
                                error_log("student_main.php Error: " . $e->getMessage());
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
                            <i class="fas fa-info-circle text-primary me-1"></i> 
                            คลิกที่หัวตารางเพื่อเรียงลำดับ • ใช้ช่องค้นหาเพื่อค้นหาผู้เรียน
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-database me-1"></i> 
                            ทั้งหมด <?= number_format($totalStudents) ?> รายการ
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
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });

        // DataTable (only when there are real data rows)
        if (typeof $.fn.DataTable !== 'undefined') {
            var hasRealRows = $('#example1 tbody tr').length > 0 &&
                              !$('#example1 tbody td[colspan]').length;

            if (hasRealRows) {
                $('#example1').DataTable({
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
                        { targets: [0, 1, 4, 5], className: 'dt-center' },
                        { targets: [5], orderable: false }
                    ]
                });
            }
        }
    });
</script>
</body>
</html>