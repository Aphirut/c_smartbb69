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
    include "./includes/function.php";
    ?>
    <title>รายละเอียดจัดซื้อจัดจ้าง | C-Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <?php
    $act_id = filter_input(INPUT_GET, 'act_id', FILTER_SANITIZE_NUMBER_INT);
    $school_id = $_SESSION['ss_school_id'];

    $activity_valid = false;
    $row = null;

    if (!empty($act_id) && is_numeric($act_id)) {
        $stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1");
        $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
        $row = $stmt->fetch();
        if ($row) {
            $activity_valid = true;
        }
    }
    ?>

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
        }

        /* ===== Modern Breadcrumb ===== */
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

        /* ===== Hero Section ===== */
        .page-hero {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(245,158,11,0.25);
            margin-bottom: 1.5rem;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .page-hero::after {
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
            font-size: 1.5rem; font-weight: 700;
            margin: 0; letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.92rem; opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.78rem; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-hero-back {
            background: white;
            color: #ef4444;
            border: none;
            padding: 0.7rem 1.4rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.25s ease;
        }
        .btn-hero-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.18);
            color: #dc2626;
        }

        /* ===== Modern Card ===== */
        .modern-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
            z-index: 1;
        }
        .modern-card .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--border-soft);
            padding: 1.1rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
            color: var(--text-dark) !important;
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
        .modern-card .card-body { padding: 1.5rem; }

        /* ===== Detail Table ===== */
        .detail-table {
            margin: 0;
        }
        .detail-table td {
            padding: 0.85rem 1rem !important;
            vertical-align: top;
            border-color: var(--border-soft) !important;
        }
        .detail-table td.label-cell {
            background: var(--bg-soft);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.875rem;
            width: 200px;
            border-right: 3px solid var(--primary) !important;
        }
        .detail-table td.label-cell i {
            color: var(--primary);
            margin-right: 0.4rem;
        }
        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
            border-bottom: 1px dashed var(--border-soft);
            font-size: 0.9rem;
        }
        .cost-item:last-child { border-bottom: none; }
        .cost-item .cost-name { color: var(--text-dark); }
        .cost-item .cost-name .text-muted-tag {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.35rem;
        }
        .cost-item .cost-value {
            font-weight: 700;
            color: var(--text-dark);
        }
        .total-row {
            background: linear-gradient(135deg, #eef2ff, #fae8ff);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-weight: 700;
            color: var(--primary);
            font-size: 1.05rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-add-procurement {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 0.55rem 1.1rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 3px 10px rgba(79,70,229,0.25);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.25s ease;
            margin-top: 0.75rem;
        }
        .btn-add-procurement:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 14px rgba(79,70,229,0.35);
            color: white;
        }

        /* ===== Budget Dashboard Cards ===== */
        .budget-section-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed var(--border-soft);
        }
        .budget-section-title h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .budget-section-title h6::before {
            content: '';
            width: 4px;
            height: 18px;
            background: linear-gradient(180deg, #4f46e5, #ec4899);
            border-radius: 4px;
        }
        .budget-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
        }
        .budget-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .budget-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 4px;
            background: var(--card-accent, var(--primary));
        }
        .budget-card.theme-primary { --card-accent: linear-gradient(180deg, #4f46e5, #7c3aed); }
        .budget-card.theme-warning { --card-accent: linear-gradient(180deg, #f59e0b, #ef4444); }

        .budget-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .budget-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .budget-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }
        .budget-value.text-primary-grad {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .budget-value.text-warning-grad {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .budget-icon {
            font-size: 1.5rem;
            opacity: 0.4;
        }
        .progress-modern {
            height: 6px;
            background: var(--bg-soft);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .progress-modern-bar {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #ef4444);
            border-radius: 999px;
            transition: width 0.6s ease;
        }
        .progress-modern-bar.over {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        /* Balance Card (Featured) */
        .balance-card {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
            box-shadow: 0 4px 14px rgba(16,185,129,0.25);
        }
        .balance-card.over-budget {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 14px rgba(239,68,68,0.3);
        }
        .balance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16,185,129,0.35);
        }
        .balance-card.over-budget:hover {
            box-shadow: 0 6px 18px rgba(239,68,68,0.4);
        }
        .balance-card::after {
            content: '';
            position: absolute;
            top: -30%; right: -10%;
            width: 150px; height: 150px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .balance-card .budget-label { color: rgba(255,255,255,0.85); }
        .balance-card .budget-value { color: white; font-size: 1.5rem; }
        .balance-card .budget-icon { opacity: 0.75; }

        /* Status Card */
        .status-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            transition: all 0.25s ease;
        }
        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .status-card .status-main {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status-card .status-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* ===== Procurement Table ===== */
        .modern-table {
            margin: 0 !important;
            font-size: 0.875rem;
        }
        .modern-table thead th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            font-weight: 700 !important;
            border: none !important;
            padding: 0.85rem 0.75rem !important;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        .modern-table tbody tr:hover {
            background: linear-gradient(90deg, #eef2ff 0%, #fae8ff 100%) !important;
        }
        .modern-table tbody td {
            padding: 0.75rem !important;
            vertical-align: middle;
            color: var(--text-dark);
            border-color: var(--border-soft) !important;
        }
        .seq-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #eef2ff, #fae8ff);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
        }
        .bill-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(6,182,212,0.25);
        }
        .amount-cell {
            text-align: right;
            font-weight: 700;
            color: var(--text-dark);
            font-variant-numeric: tabular-nums;
        }

        /* ===== Print Buttons ===== */
        .print-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; height: 30px;
            border-radius: 8px;
            font-size: 0.75rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .print-btn-success  { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
        .print-btn-success:hover  { background: #10b981; color: white; }
        .print-btn-primary  { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .print-btn-primary:hover  { background: #3b82f6; color: white; }
        .print-btn-danger   { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .print-btn-danger:hover   { background: #ef4444; color: white; }
        .print-btn-warning  { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .print-btn-warning:hover  { background: #f59e0b; color: white; }
        .print-btn-secondary{ background: #e2e8f0; color: #475569; border-color: #cbd5e1; }
        .print-btn-secondary:hover{ background: #64748b; color: white; }
        .print-btn-info     { background: #cffafe; color: #155e75; border-color: #67e8f9; }
        .print-btn-info:hover     { background: #06b6d4; color: white; }
        .print-btn-purple   { background: #f3e8ff; color: #6b21a8; border-color: #d8b4fe; }
        .print-btn-purple:hover   { background: #9333ea; color: white; }
        .print-btn-rose     { background: #ffe4e6; color: #9f1239; border-color: #fda4af; }
        .print-btn-rose:hover     { background: #e11d48; color: white; }

        /* Manage Buttons */
        .action-cell {
            white-space: nowrap;
        }
        .manage-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px; height: 34px;
            border-radius: 9px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }
        .manage-btn-edit {
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            color: white;
            box-shadow: 0 2px 6px rgba(6,182,212,0.3);
        }
        .manage-btn-edit:hover {
            transform: translateY(-2px) scale(1.05);
            color: white;
        }
        .manage-btn-delete {
            background: linear-gradient(135deg, #ef4444, #ec4899);
            color: white;
            box-shadow: 0 2px 6px rgba(239,68,68,0.3);
        }
        .manage-btn-delete:hover {
            transform: translateY(-2px) scale(1.05);
            color: white;
        }

        /* ===== Error State ===== */
        .error-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }
        .error-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #ef4444);
        }
        .error-icon {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fef3c7, #fed7aa);
            color: #f59e0b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        /* ===== Animation ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modern-card, .page-hero { animation: fadeInUp 0.4s ease-out backwards; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .page-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.2rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .detail-table td.label-cell { width: 130px; font-size: 0.8rem; }
            .modern-card .card-body { padding: 1rem; }
            .modern-table { font-size: 0.8rem; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- ===== Breadcrumb ===== -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="procurement_main.php">ข้อมูลจัดซื้อจัดจ้าง</a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold">รายละเอียด</li>
                </ol>
            </nav>

            <?php if ($activity_valid): ?>

                <!-- ===== Hero Section ===== -->
                <div class="page-hero">
                    <div class="hero-content">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hero-avatar">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <span class="hero-badge">
                                    <i class="fas fa-clipboard-list"></i> รายละเอียดโครงการ
                                </span>
                                <h1 class="hero-title mt-2">รายละเอียดข้อมูลจัดซื้อจัดจ้าง</h1>
                                <p class="hero-subtitle">ตรวจสอบงบประมาณ บันทึก และพิมพ์เอกสารการจัดซื้อจัดจ้าง</p>
                            </div>
                        </div>
                        <a href="procurement_main.php" class="btn-hero-back">
                            <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                        </a>
                    </div>
                </div>

                <?php
                try {
                    $sql = "SELECT activity.activity_id, activity.activity_name,
                                course.course_name, course.course_hour,
                                activity.date_id, activity.date_range,
                                activity.date_start, activity.date_end,
                                activity.time_start, activity.time_end,
                                lecturer_cost, material_cost,
                                board_cost, food_cost,
                                drink_cost, place_cost,
                                car_cost, other_cost,
                                total_costs
                            FROM activity
                            INNER JOIN course ON activity.course_id = course.course_id
                            WHERE activity.activity_id = :act_id AND activity.school_id = :school_id LIMIT 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
                    $row = $stmt->fetch();
                } catch (PDOException $e) {
                    error_log("procurement_show.php detail query Error: " . $e->getMessage());
                    $row = false;
                }

                if (!$row) {
                    echo '<div class="alert alert-danger">ไม่พบข้อมูลโครงการ กรุณาลองใหม่อีกครั้ง</div>';
                    $activity_id = $act_id;
                    $activity_name = '';
                    $course_name   = '';
                    $course_hour   = 0;
                    $total_costs   = 0;
                    $date = '';
                } else {
                    $activity_id = $row['activity_id'];

                    if ($row['date_id'] == "1") {
                        if ($row['date_start'] == $row['date_end']) {
                            $date = ADYearToBEYearTH($row['date_start'], 2);
                        } else {
                            $date = ADYearToBEYearTH($row['date_start'], 2) . " ถึง " . ADYearToBEYearTH($row['date_end'], 2);
                        }
                    } elseif ($row['date_id'] == "2") {
                        $date = DateRangeToString($row['date_range'], 2);
                    } else {
                        $date = 'ไม่ระบุวันที่';
                    }
                    if (empty($date)) {
                        $date = 'ไม่ระบุวันที่';
                    }

                    $activity_name = $row['activity_name'];
                    $course_name   = $row['course_name'];
                    $course_hour   = $row['course_hour'];
                    $total_costs   = $row['total_costs'];
                }

                // === คำนวณงบประมาณค่าวัสดุ ===
                $material_budget = ($row && isset($row['material_cost'])) ? (float)$row['material_cost'] : 0;
                $used_material = 0;
                $material_bill_count = 0;

                try {
                    $stmt_used = $pdo->prepare("
                        SELECT
                            COALESCE(SUM(procurement_paid), 0) AS used_material,
                            COUNT(*) AS bill_count
                        FROM procurement
                        WHERE activity_id = :act_id AND expense_id = 1
                    ");
                    $stmt_used->execute(['act_id' => $act_id]);
                    $used_row = $stmt_used->fetch();
                    if ($used_row) {
                        $used_material = (float)($used_row['used_material'] ?? 0);
                        $material_bill_count = (int)($used_row['bill_count'] ?? 0);
                    }
                } catch (PDOException $e) {}

                $remaining_material = $material_budget - $used_material;
                $is_over_budget = $remaining_material < 0;
                $used_percent = $material_budget > 0
                    ? min(100, round(($used_material / $material_budget) * 100, 1))
                    : 0;
                ?>

                <!-- ===== Activity Detail Card ===== -->
                <div class="modern-card">
                    <div class="card-header">
                        <h5>
                            <span class="card-header-icon"><i class="fas fa-info-circle"></i></span>
                            รายละเอียดโครงการ
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table detail-table mb-0">
                            <tbody>
                                <tr>
                                    <td class="label-cell"><i class="fas fa-bookmark"></i>ชื่อโครงการ</td>
                                    <td><strong style="font-size: 1.05rem;"><?= htmlspecialchars($activity_name) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="label-cell"><i class="fas fa-graduation-cap"></i>หลักสูตร</td>
                                    <td>
                                        <?= htmlspecialchars($course_name) ?>
                                        <span class="badge bg-primary-subtle text-primary ms-2">
                                            <i class="far fa-clock me-1"></i><?= $course_hour ?> ชั่วโมง
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-cell"><i class="far fa-calendar-alt"></i>วันที่และเวลา</td>
                                    <td>
                                        <?= htmlspecialchars($date) ?>
                                        <span class="badge bg-info-subtle text-info ms-2">
                                            <i class="far fa-clock me-1"></i>
                                            <?= htmlspecialchars($row['time_start']) ?> - <?= htmlspecialchars($row['time_end']) ?> น.
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-cell"><i class="fas fa-coins"></i>ค่าใช้จ่ายโครงการ</td>
                                    <td>
                                        <?php if ($total_costs != "0"): ?>
                                            <div class="total-row">
                                                <span><i class="fas fa-money-bill-wave me-2"></i>รวมทั้งสิ้น</span>
                                                <span><?= number_format($total_costs, 2) ?> บาท</span>
                                            </div>
                                            <?php if ($row['lecturer_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าวิทยากร<span class="text-muted-tag">(ไม่ต้องจัดซื้อ)</span></span>
                                                    <span class="cost-value"><?= number_format($row['lecturer_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['material_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าวัสดุ</span>
                                                    <span class="cost-value"><?= number_format($row['material_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['board_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าป้าย</span>
                                                    <span class="cost-value"><?= number_format($row['board_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['food_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าอาหาร<span class="text-muted-tag">(ไม่ต้องจัดซื้อ)</span></span>
                                                    <span class="cost-value"><?= number_format($row['food_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['drink_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าอาหารว่าง<span class="text-muted-tag">(ไม่ต้องจัดซื้อ)</span></span>
                                                    <span class="cost-value"><?= number_format($row['drink_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['place_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าสถานที่</span>
                                                    <span class="cost-value"><?= number_format($row['place_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['car_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าจ้างเหมารถ</span>
                                                    <span class="cost-value"><?= number_format($row['car_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($row['other_cost'] != "0"): ?>
                                                <div class="cost-item">
                                                    <span class="cost-name">ค่าใช้จ่ายอื่น</span>
                                                    <span class="cost-value"><?= number_format($row['other_cost'], 2) ?> บาท</span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <a href="procurement_in.php?act_id=<?= $activity_id ?>"
                                           class="btn-add-procurement"
                                           data-bs-toggle="tooltip"
                                           title="บันทึกรายการจัดซื้อจัดจ้าง">
                                            <i class="fas fa-plus-circle"></i> บันทึกรายการจัดซื้อจัดจ้าง
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ===== Budget Dashboard ===== -->
                <div class="modern-card">
                    <div class="card-header">
                        <h5>
                            <span class="card-header-icon" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
                                <i class="fas fa-wallet"></i>
                            </span>
                            สรุปงบประมาณค่าวัสดุ
                        </h5>
                        <span class="badge bg-light text-muted ms-auto">
                            <i class="fas fa-tag me-1"></i>Expense ID = 1
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- งบประมาณทั้งหมด -->
                            <div class="col-6 col-md-3">
                                <div class="budget-card theme-primary">
                                    <div class="budget-card-header">
                                        <div>
                                            <div class="budget-label">งบประมาณค่าวัสดุ</div>
                                            <div class="budget-value text-primary-grad">
                                                <?= number_format($material_budget, 2) ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-coins budget-icon text-primary"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- ใช้ไปแล้ว -->
                            <div class="col-6 col-md-3">
                                <div class="budget-card theme-warning">
                                    <div class="budget-card-header">
                                        <div>
                                            <div class="budget-label">
                                                ใช้ไปแล้ว
                                                <span class="badge bg-warning-subtle text-warning ms-1">
                                                    <?= $material_bill_count ?> บิล
                                                </span>
                                            </div>
                                            <div class="budget-value text-warning-grad">
                                                <?= number_format($used_material, 2) ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-receipt budget-icon text-warning"></i>
                                    </div>
                                    <div class="progress-modern">
                                        <div class="progress-modern-bar <?= $used_percent >= 100 ? 'over' : '' ?>"
                                             style="width: <?= $used_percent ?>%"></div>
                                    </div>
                                    <div class="text-end mt-1">
                                        <small class="text-muted fw-semibold"><?= $used_percent ?>%</small>
                                    </div>
                                </div>
                            </div>

                            <!-- คงเหลือ -->
                            <div class="col-6 col-md-3">
                                <div class="balance-card <?= $is_over_budget ? 'over-budget' : '' ?>">
                                    <div class="budget-card-header">
                                        <div>
                                            <div class="budget-label">
                                                <?= $is_over_budget ? 'เกินงบประมาณ' : 'คงเหลือ' ?>
                                            </div>
                                            <div class="budget-value">
                                                <?= number_format($remaining_material, 2) ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-<?= $is_over_budget ? 'exclamation-triangle' : 'piggy-bank' ?> budget-icon"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- สถานะ -->
                            <div class="col-6 col-md-3">
                                <div class="status-card">
                                    <?php if ($is_over_budget): ?>
                                        <div class="status-main text-danger">
                                            <i class="fas fa-exclamation-circle"></i> เกินงบ
                                        </div>
                                        <div class="status-sub">โปรดตรวจสอบรายการ</div>
                                    <?php elseif ($remaining_material == 0 && $material_budget > 0): ?>
                                        <div class="status-main text-warning">
                                            <i class="fas fa-check-circle"></i> ใช้หมดแล้ว
                                        </div>
                                        <div class="status-sub">งบประมาณถูกใช้เต็มจำนวน</div>
                                    <?php elseif ($material_budget == 0): ?>
                                        <div class="status-main text-muted">
                                            <i class="fas fa-info-circle"></i> ไม่ได้ตั้งงบ
                                        </div>
                                        <div class="status-sub">ไม่มีการตั้งงบค่าวัสดุ</div>
                                    <?php else: ?>
                                        <div class="status-main text-success">
                                            <i class="fas fa-check-circle"></i> ยังมีงบเหลือ
                                        </div>
                                        <div class="status-sub">สามารถจัดซื้อเพิ่มได้</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Procurement Items Table ===== -->
                <div class="modern-card">
                    <div class="card-header">
                        <h5>
                            <span class="card-header-icon"><i class="fas fa-list-ul"></i></span>
                            รายการจัดซื้อจัดจ้าง
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px">ลำดับ</th>
                                        <th class="text-center">รายการ</th>
                                        <th class="text-center">ผู้ขาย/ร้านค้า</th>
                                        <th class="text-center" style="width: 140px">เลขที่บิล</th>
                                        <th class="text-center" style="width: 130px">จำนวนเงิน</th>
                                        <th class="text-center" style="min-width: 280px">พิมพ์เอกสาร</th>
                                        <th class="text-center" style="width: 100px">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $check = false;
                                    $sql1 = "SELECT COUNT(procurement.procurement_id) AS material_number,
                                                    procurement.expense_id
                                                FROM procurement
                                                WHERE activity_id = :act_id AND expense_id = '1'
                                                GROUP BY procurement.expense_id";
                                    $stmt = $pdo->prepare($sql1);
                                    $stmt->execute(['act_id' => $act_id]);
                                    $row_mat = $stmt->fetch();
                                    $material_number = $row_mat['material_number'] ?? 0;

                                    $a = 0;
                                    $sql2 = "SELECT procurement.activity_id, procurement.expense_id,
                                                    procurement.procurement_id, procurement.procurement_paid,
                                                    procurement.bill_number,
                                                    expense.expense_name, payee.payee_name
                                                FROM procurement
                                                INNER JOIN payee ON payee.payee_id = procurement.payee_id
                                                INNER JOIN expense ON expense.expense_id = procurement.expense_id
                                                WHERE procurement.activity_id = :act_id
                                                ORDER BY procurement.expense_id ASC";
                                    $stmt = $pdo->prepare($sql2);
                                    $stmt->execute(['act_id' => $act_id]);

                                    $hasRow = false;
                                    while ($r = $stmt->fetch()) {
                                        $hasRow = true;
                                        if ($material_number > 1 && $r['expense_id'] == 1) {
                                            echo "<tr>";
                                            if (!$check) {
                                                $a++;
                                                echo "<td class='text-center'><span class='seq-badge'>$a</span></td>";
                                            } else {
                                                echo "<td class='text-center'></td>";
                                            }
                                            echo "<td class='text-center fw-semibold'>".htmlspecialchars($r['expense_name'])."</td>";
                                            echo "<td>".htmlspecialchars($r['payee_name'])."</td>";
                                            echo "<td class='text-center'>" . ($r['bill_number'] ? '<span class="bill-badge">' . htmlspecialchars($r['bill_number']) . '</span>' : '<span class="text-muted">-</span>') . "</td>";
                                            echo "<td class='amount-cell'>" . number_format($r['procurement_paid'], 2) . "</td>";
                                            echo "<td><div class='print-buttons'>";
                                            if (!$check) {
                                                echo "<a href='reports/procurement_permission_sum.php?act_id=".$r['activity_id']."' target='_blank' class='print-btn print-btn-success' title='ขออนุญาต'><i class='fas fa-print'></i></a>";
                                                echo "<a href='reports/procurement_report_sum.php?act_id=".$r['activity_id']."' target='_blank' class='print-btn print-btn-primary' title='รายงานขอ'><i class='fas fa-print'></i></a>";
                                                echo "<a href='reports/procurement_consider_sum.php?act_id=".$r['activity_id']."' target='_blank' class='print-btn print-btn-danger' title='พิจารณา'><i class='fas fa-print'></i></a>";
                                                echo "<a href='reports/procurement_check_sum.php?act_id=".$r['activity_id']."' target='_blank' class='print-btn print-btn-warning' title='ตรวจรับ'><i class='fas fa-print'></i></a>";
                                                echo "<a href='reports/procurement_disburse_sum.php?act_id=".$r['activity_id']."' target='_blank' class='print-btn print-btn-secondary' title='ขอเบิก'><i class='fas fa-print'></i></a>";
                                            }
                                            echo "</div></td>";
                                            echo "<td class='text-center action-cell'>";
                                            echo "<div class='d-inline-flex gap-1'>";
                                            echo "<a href='procurement_in.php?submit=EDIT&edit_id=".$r['procurement_id']."&act_id=$act_id' class='manage-btn manage-btn-edit' data-bs-toggle='tooltip' title='แก้ไข'><i class='fas fa-edit'></i></a>";
                                            echo "<a href='procurement_del.php?act_id=$act_id&del_id=".$r['procurement_id']."' onclick='return CheckDel()' class='manage-btn manage-btn-delete' data-bs-toggle='tooltip' title='ลบ'><i class='fas fa-trash-alt'></i></a>";
                                            echo "</div>";
                                            echo "</td></tr>";
                                            $check = true;
                                        } else {
                                            $a++;
                                            echo "<tr>";
                                            echo "<td class='text-center'><span class='seq-badge'>$a</span></td>";
                                            echo "<td class='text-center fw-semibold'>".htmlspecialchars($r['expense_name'])."</td>";
                                            echo "<td>".htmlspecialchars($r['payee_name'])."</td>";
                                            echo "<td class='text-center'>" . ($r['bill_number'] ? '<span class="bill-badge">' . htmlspecialchars($r['bill_number']) . '</span>' : '<span class="text-muted">-</span>') . "</td>";
                                            echo "<td class='amount-cell'>" . number_format($r['procurement_paid'], 2) . "</td>";
                                            echo "<td><div class='print-buttons'>";
                                            echo "<a href='reports/procurement_permission.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-success' title='ขออนุญาต'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_report.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-primary' title='รายงานขอ'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_consider.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-danger' title='พิจารณา'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_check.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-warning' title='ตรวจรับ'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_disburse.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-secondary' title='ขอเบิก'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_disburse_b.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-info' title='ขอเบิก(ยืม)'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/procurement_disburse_j.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-purple' title='ขอเบิก(จังหวัด)'><i class='fas fa-print'></i></a>";
                                            echo "<a href='reports/generate_pdf.php?act_id=".$r['activity_id']."&proc_id=".$r['procurement_id']."' target='_blank' class='print-btn print-btn-rose' title='งบหน้าใบสำคัญเบิก'><i class='fas fa-print'></i></a>";
                                            echo "</div></td>";
                                            echo "<td class='text-center action-cell'>";
                                            echo "<div class='d-inline-flex gap-1'>";
                                            echo "<a href='procurement_in.php?submit=EDIT&edit_id=".$r['procurement_id']."&act_id=$act_id' class='manage-btn manage-btn-edit' data-bs-toggle='tooltip' title='แก้ไข'><i class='fas fa-edit'></i></a>";
                                            echo "<a href='procurement_del.php?act_id=$act_id&del_id=".$r['procurement_id']."' onclick='return CheckDel()' class='manage-btn manage-btn-delete' data-bs-toggle='tooltip' title='ลบ'><i class='fas fa-trash-alt'></i></a>";
                                            echo "</div>";
                                            echo "</td></tr>";
                                        }
                                    }

                                    if (!$hasRow) {
                                        echo '<tr><td colspan="7" class="text-center text-muted py-5">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                                                ยังไม่มีรายการจัดซื้อจัดจ้าง คลิกปุ่ม <strong>"บันทึกรายการจัดซื้อจัดจ้าง"</strong> ด้านบนเพื่อเพิ่มรายการ
                                              </td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <!-- ===== Error State ===== -->
                <div class="row justify-content-center mt-4">
                    <div class="col-md-8 col-lg-6">
                        <div class="error-card">
                            <div class="error-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--text-dark);">ไม่พบข้อมูลกิจกรรม</h4>
                            <p class="text-muted mb-4">
                                ไม่พบกิจกรรมที่ต้องการดูรายละเอียดจัดซื้อจัดจ้าง<br>
                                หรือคุณไม่มีสิทธิ์เข้าถึงกิจกรรมนี้
                            </p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="procurement_main.php" class="btn-add-procurement" style="margin-top:0;">
                                    <i class="fas fa-list"></i> กลับไปหน้ารายการ
                                </a>
                                <a href="index.php" class="btn-hero-back" style="color:var(--text-muted);">
                                    <i class="fas fa-home"></i> ไปหน้าหลัก
                                </a>
                            </div>
                            <div class="mt-4 small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                หากคุณคลิกลิงก์จากที่อื่น กรุณากลับไปที่เมนู <strong>ข้อมูลจัดซื้อจัดจ้าง</strong> แล้วเลือกกิจกรรมใหม่
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    function CheckDel() {
        return confirm('⚠️ กรุณายืนยันการลบอีกครั้ง !!!');
    }

    $(function () {
        // เปิดใช้งาน Tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>

</body>
</html>