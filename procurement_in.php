<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";
    include "./includes/function.php";

    // Sanitize and get activity ID
    $act_id = $_REQUEST['act_id'] ?? null;

    if (!is_numeric($act_id)) {
        echo "<meta http-equiv='refresh' content='0;url=index.php'>";
        exit;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1");
        $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
        $activity = $stmt->fetch();

        if (!$activity) {
            echo "<meta http-equiv='refresh' content='0;url=index.php'>";
            exit;
        }

        // === คำนวณงบประมาณค่าวัสดุคงเหลือ ===
        $material_budget_in = (float)($activity['material_cost'] ?? 0);
        $used_material_in = 0;
        $material_bill_count_in = 0;

        try {
            $stmt_used = $pdo->prepare("
                SELECT 
                    COALESCE(SUM(procurement_paid), 0) AS used,
                    COUNT(*) AS bill_count
                FROM procurement 
                WHERE activity_id = :act_id AND expense_id = 1
            ");
            $stmt_used->execute(['act_id' => $act_id]);
            $used_row = $stmt_used->fetch();
            if ($used_row) {
                $used_material_in = (float)($used_row['used'] ?? 0);
                $material_bill_count_in = (int)($used_row['bill_count'] ?? 0);
            }
        } catch (PDOException $e) {}

        $remaining_in = $material_budget_in - $used_material_in;
        $is_over_in = $remaining_in < 0;
        $used_percent_in = $material_budget_in > 0 
            ? min(100, round(($used_material_in / $material_budget_in) * 100, 1)) 
            : 0;

        // สถานะแดชบอร์ด
        if ($is_over_in) {
            $dash_gradient = 'linear-gradient(135deg, #ef4444, #dc2626)';
            $dash_icon = 'exclamation-triangle';
            $dash_status = 'เกินงบ';
            $dash_badge_class = 'bg-danger';
        } elseif ($used_percent_in >= 90) {
            $dash_gradient = 'linear-gradient(135deg, #ef4444, #f59e0b)';
            $dash_icon = 'exclamation-circle';
            $dash_status = 'ใช้สูงมาก';
            $dash_badge_class = 'bg-danger';
        } elseif ($used_percent_in >= 80) {
            $dash_gradient = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
            $dash_icon = 'exclamation-circle';
            $dash_status = 'ใช้สูง';
            $dash_badge_class = 'bg-warning text-dark';
        } else {
            $dash_gradient = 'linear-gradient(135deg, #10b981, #059669)';
            $dash_icon = 'check-circle';
            $dash_status = 'ปกติ';
            $dash_badge_class = 'bg-success';
        }

        // Sanitize and get edit ID
        $edit_id = $_REQUEST['edit_id'] ?? null;
        $procurement_id = null;
        $expense_id = null;
        $amount = null;
        $payee_id = null;
        $bill_number = null;
        $procurement_paid = null;
        $inspector_name = null;
        $inspector_position_id = null;
        $use_date = null;

        // Load dropdown data
        $expenses = [];
        $payees = [];
        try {
            $exp_stmt = $pdo->query("SELECT expense_id, expense_name FROM expense ORDER BY expense_id ASC");
            $expenses = $exp_stmt->fetchAll();

            $pay_stmt = $pdo->prepare("SELECT payee_id, payee_name FROM payee WHERE school_id = :school_id ORDER BY payee_name ASC");
            $pay_stmt->execute(['school_id' => $_SESSION['ss_school_id']]);
            $payees = $pay_stmt->fetchAll();
        } catch (PDOException $e) {}

        if ($edit_id !== null && is_numeric($edit_id)) {
            $stmt = $pdo->prepare("SELECT * FROM procurement WHERE procurement_id = :edit_id AND school_id = :school_id AND activity_id = :act_id LIMIT 1");
            $stmt->execute(['edit_id' => $edit_id, 'school_id' => $_SESSION['ss_school_id'], 'act_id' => $act_id]);
            $procurement = $stmt->fetch();

            if ($procurement) {
                $procurement_id        = $procurement['procurement_id'];
                $expense_id            = $procurement['expense_id'];
                $amount                = $procurement['amount'];
                $payee_id              = $procurement['payee_id'];
                $bill_number           = $procurement['bill_number'] ?? '';
                $procurement_paid      = $procurement['procurement_paid'];
                $inspector_name        = $procurement['inspector_name'];
                $inspector_position_id = $procurement['inspector_position_id'];
                $use_date              = ADYearToBEYear($procurement['use_date']);
            } else {
                echo "<meta http-equiv='refresh' content='0;url=index.php'>";
                exit;
            }
        }

        $isEditMode = !empty($procurement_id);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>

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
        .proc-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .proc-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .proc-hero::after {
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

        /* ===== Budget Dashboard ===== */
        .budget-dashboard {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.4s ease-out;
        }
        .budget-dashboard-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-soft);
        }
        .budget-icon-wrap {
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
        .budget-title-text h6 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }
        .budget-title-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }

        .stats-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.1rem;
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
        .stats-card.budget::before { background: linear-gradient(90deg, #4f46e5, #7c3aed); }
        .stats-card.used::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }

        .stats-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            margin-bottom: 0.75rem;
        }
        .stats-card.budget .stats-icon { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stats-card.used .stats-icon { background: linear-gradient(135deg, #f59e0b, #ef4444); }

        .stats-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .stats-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .stats-unit {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Remaining Card (Big) */
        .remaining-card {
            background: <?= $dash_gradient ?>;
            color: white;
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .remaining-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            border-radius: 50%;
        }
        .remaining-label {
            font-size: 0.85rem;
            opacity: 0.95;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }
        .remaining-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1px;
            position: relative;
            z-index: 2;
        }
        .remaining-icon {
            font-size: 3rem;
            opacity: 0.5;
            position: relative;
            z-index: 2;
        }

        /* Progress Card */
        .progress-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.1rem 1.25rem;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .modern-progress {
            height: 12px;
            border-radius: 999px;
            background: var(--bg-soft);
            overflow: hidden;
        }
        .modern-progress-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 0.6s ease;
        }

        /* ===== Form Card ===== */
        .form-card {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            overflow: hidden;
            animation: fadeInUp 0.4s ease-out;
        }
        .form-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to right, #fafbff, white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .form-card-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .form-icon-wrap {
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
        .form-title-text h6 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }
        .form-title-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }
        .form-card-body { padding: 1.75rem 1.5rem; }

        /* ===== Section ===== */
        .form-section {
            margin-bottom: 1.75rem;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 2px solid var(--primary-light);
        }
        .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* ===== Form Controls ===== */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .form-control, .form-select {
            border: 1.5px solid var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 0.6rem 0.9rem;
            font-size: 0.92rem;
            background: var(--bg-soft);
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
            background: white;
        }
        .form-control[readonly] {
            background: #f1f5f9;
            cursor: not-allowed;
        }
        .form-text {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        /* Select2 override */
        .select2-container--default .select2-selection--single {
            border: 1.5px solid var(--border-soft) !important;
            border-radius: var(--radius-sm) !important;
            height: calc(1.5em + 1.2rem + 3px) !important;
            background: var(--bg-soft) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 1.2rem + 3px) !important;
            padding-left: 0.9rem !important;
            color: var(--text-dark);
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 1.2rem + 3px) !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12) !important;
            background: white !important;
        }

        /* ===== Material Table ===== */
        .material-table-wrap {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-soft);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        #material_table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        #material_table thead th {
            background: linear-gradient(135deg, #f8fafc, #eef2ff) !important;
            color: var(--text-dark) !important;
            font-weight: 700 !important;
            border: none !important;
            border-bottom: 2px solid var(--border-soft) !important;
            padding: 0.85rem 0.75rem !important;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
        }
        #material_table tbody td {
            padding: 0.6rem 0.65rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            vertical-align: middle;
        }
        #material_table tbody tr {
            transition: background 0.2s ease;
        }
        #material_table tbody tr:hover {
            background-color: #fafbff !important;
        }
        #material_table tbody tr:last-child td {
            border-bottom: none !important;
        }
        #material_table tfoot td {
            background: linear-gradient(135deg, #fafbff, #eef2ff) !important;
            border-top: 2px solid var(--border-soft) !important;
            padding: 0.9rem 0.75rem !important;
            font-size: 1rem;
        }
        #material_table input.form-control {
            padding: 0.45rem 0.65rem;
            font-size: 0.9rem;
        }
        .row-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
        }
        .btn-delete-row {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fef2f2;
            color: var(--danger);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-delete-row:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
        }

        /* ===== Buttons ===== */
        .btn-modern {
            border-radius: var(--radius-sm);
            padding: 0.65rem 1.25rem;
            font-weight: 600;
            font-size: 0.92rem;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .btn-modern-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }
        .btn-modern-primary:hover { color: white; }
        .btn-modern-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .btn-modern-success:hover { color: white; }
        .btn-modern-secondary {
            background: white;
            color: var(--text-muted);
            border: 1.5px solid var(--border-soft);
        }
        .btn-modern-secondary:hover {
            background: var(--bg-soft);
            color: var(--text-dark);
        }
        .btn-modern-warning {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
        }
        .btn-modern-warning:hover { color: white; }
        .btn-modern-outline {
            background: white;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .btn-modern-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* ===== Alerts ===== */
        .alert-modern {
            border: none;
            border-left: 4px solid;
            border-radius: var(--radius-sm);
            padding: 1rem 1.25rem;
            font-weight: 500;
        }
        .alert-modern.alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left-color: var(--success);
        }

        /* ===== Tip Box ===== */
        .tip-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-left: 4px solid var(--warning);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: #78350f;
        }

        /* ===== Cleave Inputs ===== */
        .material_prices {
            background: linear-gradient(to right, #eef2ff, #f5f3ff) !important;
            color: var(--primary) !important;
            font-weight: 700 !important;
        }
        #procurement_paid {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            font-weight: 800 !important;
            font-size: 1.05rem !important;
            text-align: right;
            border: none !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .proc-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .form-card-body { padding: 1.25rem 1rem; }
            .remaining-value { font-size: 1.5rem; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <li class="breadcrumb-item"><a href="procurement_main.php">ข้อมูลจัดซื้อจัดจ้าง</a></li>
                    <li class="breadcrumb-item"><a href="procurement_show.php?act_id=<?= $act_id ?>">รายละเอียด</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= $isEditMode ? 'แก้ไขรายการ' : 'บันทึกรายการ' ?></li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="proc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-<?= $isEditMode ? 'edit' : 'plus-circle' ?>"></i>
                                <?= $isEditMode ? 'แก้ไขรายการจัดซื้อ' : 'บันทึกรายการจัดซื้อ' ?>
                            </span>
                            <h1 class="hero-title mt-2">บันทึกรายการจัดซื้อจัดจ้าง</h1>
                            <p class="hero-subtitle">กรอกข้อมูลบิล รายการพัสดุ และผู้ตรวจรับให้ครบถ้วน</p>
                        </div>
                    </div>
                    <a href="procurement_show.php?act_id=<?= $act_id ?>" class="btn-hero-back">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                </div>
            </div>

            <!-- แดชบอร์ดยอดเงินค่าวัสดุ -->
            <?php if ($material_budget_in > 0): ?>
            <div class="budget-dashboard">
                <div class="budget-dashboard-title">
                    <div class="budget-icon-wrap">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="budget-title-text">
                        <h6>สถานะงบประมาณค่าวัสดุ</h6>
                        <p>ติดตามการใช้งบประมาณค่าวัสดุของกิจกรรมนี้ (Expense ID = 1)</p>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- งบทั้งหมด -->
                    <div class="col-6 col-lg-2">
                        <div class="stats-card budget">
                            <div class="stats-icon"><i class="fas fa-coins"></i></div>
                            <div class="stats-label">งบประมาณทั้งหมด</div>
                            <div class="stats-value"><?= number_format($material_budget_in, 0) ?></div>
                            <div class="stats-unit">บาท</div>
                        </div>
                    </div>

                    <!-- ใช้ไปแล้ว -->
                    <div class="col-6 col-lg-2">
                        <div class="stats-card used">
                            <div class="stats-icon"><i class="fas fa-receipt"></i></div>
                            <div class="stats-label">ใช้ไปแล้ว</div>
                            <div class="stats-value"><?= number_format($used_material_in, 0) ?></div>
                            <div class="stats-unit">บาท • <?= $material_bill_count_in ?> บิล</div>
                        </div>
                    </div>

                    <!-- คงเหลือ (เด่นมาก) -->
                    <div class="col-12 col-lg-5">
                        <div class="remaining-card">
                            <div>
                                <div class="remaining-label">คงเหลือ</div>
                                <div class="remaining-value">
                                    <?= number_format($remaining_in, 0) ?> <small style="font-size: 1rem; opacity: 0.9;">บาท</small>
                                </div>
                                <div class="remaining-label mt-1">
                                    <i class="fas fa-info-circle me-1"></i> สถานะ: <?= $dash_status ?>
                                </div>
                            </div>
                            <div class="remaining-icon">
                                <i class="fas fa-<?= $dash_icon ?>"></i>
                            </div>
                        </div>
                    </div>

                    <!-- เปอร์เซ็นต์ -->
                    <div class="col-12 col-lg-3">
                        <div class="progress-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted fw-semibold">การใช้งบประมาณ</span>
                                <span class="badge <?= $dash_badge_class ?> px-3 py-2 fw-bold" style="border-radius: 999px;">
                                    <?= $used_percent_in ?>%
                                </span>
                            </div>
                            <div class="modern-progress">
                                <div class="modern-progress-bar"
                                     style="width: <?= $used_percent_in ?>%; background: <?= $dash_gradient ?>;"
                                     role="progressbar"
                                     aria-valuenow="<?= $used_percent_in ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="mt-2 small fw-semibold" style="color: <?= $is_over_in ? '#dc2626' : ($used_percent_in >= 80 ? '#d97706' : '#059669') ?>;">
                                <i class="fas fa-<?= $dash_icon ?> me-1"></i> <?= $dash_status ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Main Form Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-title">
                        <div class="form-icon-wrap">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="form-title-text">
                            <h6><?= $isEditMode ? 'แก้ไขข้อมูลบิล' : 'แบบฟอร์มบันทึกบิลใหม่' ?></h6>
                            <p>กรอกข้อมูลให้ครบถ้วน เพื่อบันทึกการจัดซื้อจัดจ้าง</p>
                        </div>
                    </div>
                </div>

                <div class="form-card-body">
                    <?php if (isset($_GET['added']) && $_GET['added'] == '1'): ?>
                        <div class="alert alert-modern alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>บันทึกบิลก่อนหน้าเรียบร้อยแล้ว!</strong> กรุณาเลือกผู้ขาย/ร้านค้าใหม่ และกรอกรายการบิลถัดไป
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="form1" action="procurement_save.php" method="post">
                        <input type="hidden" name="act_id" value="<?= $act_id ?>">
                        <input type="hidden" name="procurement_id" value="<?= htmlspecialchars($procurement_id ?? '') ?>">
                        <input type="hidden" name="activity_id" value="<?= $act_id ?>">

                        <!-- Section: วันที่ใช้พัสดุ -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-calendar-alt"></i></div>
                                <h6 class="section-title">วันที่ใช้พัสดุ</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">วันที่ใช้พัสดุ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="use_date" name="use_date"
                                           required autocomplete="off"
                                           value="<?= htmlspecialchars($use_date ?? '') ?>"
                                           placeholder="วว/ดด/ปปปป">
                                </div>
                            </div>
                        </div>

                        <!-- Section: ข้อมูลบิล -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-file-invoice"></i></div>
                                <h6 class="section-title">ข้อมูลบิล / ผู้ขาย</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ประเภทค่าใช้จ่าย <span class="text-danger">*</span></label>
                                    <select name="expense_id" class="form-select" id="expense_id" required>
                                        <option value="">-- เลือกประเภท --</option>
                                        <?php
                                        foreach ($expenses as $e) {
                                            $sel = ((string)$e['expense_id'] === (string)$expense_id) ? 'selected' : '';
                                            echo "<option value='{$e['expense_id']}' {$sel}>" . htmlspecialchars($e['expense_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ผู้ขาย/ร้านค้า <span class="text-danger">*</span></label>
                                    <select name="payee_id" class="form-select" id="payee_id" required>
                                        <option value="">-- เลือกผู้ขาย --</option>
                                        <?php
                                        foreach ($payees as $p) {
                                            $sel = ((string)$p['payee_id'] === (string)$payee_id) ? 'selected' : '';
                                            echo "<option value='{$p['payee_id']}' {$sel}>" . htmlspecialchars($p['payee_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เลขที่บิล / ใบเสร็จ / ใบกำกับภาษี</label>
                                    <input type="text" class="form-control" name="bill_number"
                                           value="<?= htmlspecialchars($bill_number ?? '') ?>"
                                           placeholder="เช่น INV-2568-00123 หรือ บิลเลขที่ 123/2568" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">จำนวนรายการวัสดุที่ต้องการ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="material_row_count" min="0" max="50" value="1">
                                        <button type="button" class="btn btn-modern btn-modern-outline" id="btn_set_material_count">
                                            <i class="fas fa-sync-alt"></i> ปรับจำนวน
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i> กรอกจำนวนที่ต้องการ แล้วกด "ปรับจำนวน" เพื่อสร้างช่องกรอกข้อมูล
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: ผู้ตรวจรับ -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-user-check"></i></div>
                                <h6 class="section-title">ผู้ตรวจรับ</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ตรวจรับ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="inspector_name"
                                           value="<?= htmlspecialchars($inspector_name ?? '') ?>" 
                                           placeholder="กรอกชื่อ-นามสกุล ผู้ตรวจรับ"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ตำแหน่ง <span class="text-danger">*</span></label>
                                    <select class="form-select" name="inspector_position_id" id="inspector_position_id" required>
                                        <option value="">-- เลือกตำแหน่ง --</option>
                                        <?php
                                        try {
                                            $pos_stmt = $pdo->query("SELECT * FROM position ORDER BY position_name");
                                            while ($p = $pos_stmt->fetch()) {
                                                $sel = ($p['position_id'] == ($inspector_position_id ?? '')) ? 'selected' : '';
                                                echo "<option value='{$p['position_id']}' {$sel}>" . htmlspecialchars($p['position_name']) . "</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<option value=''>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section: รายการพัสดุ -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-icon"><i class="fas fa-boxes"></i></div>
                                <h6 class="section-title">รายการพัสดุ</h6>
                            </div>

                            <div class="material-table-wrap mb-3">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0" id="material_table">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">ลำดับ</th>
                                                <th>รายการ</th>
                                                <th style="width:90px;">จำนวน</th>
                                                <th style="width:110px;">หน่วย</th>
                                                <th style="width:125px;">ราคา</th>
                                                <th style="width:140px;">รวม</th>
                                                <th style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="material_tbody">
                                            <?php
                                            try {
                                                $data = [];
                                                if (!empty($procurement_id)) {
                                                    $sql = "SELECT material_name, material_amount, material_unit, material_price, material_prices
                                                            FROM material
                                                            WHERE procurement_id = :procurement_id
                                                            ORDER BY material_id ASC";
                                                    $stmt = $pdo->prepare($sql);
                                                    $stmt->execute(['procurement_id' => $procurement_id]);
                                                    $data = $stmt->fetchAll();
                                                }

                                                $totalRows = count($data) > 0 ? count($data) : 1;

                                                for ($a = 0; $a < $totalRows; $a++) {
                                                    $row = $data[$a] ?? null;
                                                    $materialName   = $row['material_name']   ?? '';
                                                    $materialAmount = $row['material_amount'] ?? '';
                                                    $materialUnit   = $row['material_unit']   ?? '';
                                                    $materialPrice  = $row['material_price']  ?? '';
                                                    $materialPrices = $row['material_prices'] ?? '';
                                                    $rowNum = $a + 1;
                                            ?>
                                            <tr class="material_list_show">
                                                <td class="text-center align-middle">
                                                    <span class="row-num"><?= $rowNum ?></span>
                                                </td>
                                                <td><input type="text" class="form-control material_name" name="material_name[]" value="<?= htmlspecialchars($materialName) ?>" placeholder="ชื่อรายการพัสดุ" autocomplete="off"></td>
                                                <td><input type="text" class="form-control material_amount text-center" name="material_amount[]" value="<?= htmlspecialchars($materialAmount) ?>" maxlength="5" autocomplete="off"></td>
                                                <td><input type="text" class="form-control material_unit text-center" name="material_unit[]" value="<?= htmlspecialchars($materialUnit) ?>" maxlength="20" placeholder="เช่น ชิ้น" autocomplete="off"></td>
                                                <td><input type="text" class="form-control material_price text-end" name="material_price[]" value="<?= htmlspecialchars($materialPrice) ?>" maxlength="12" autocomplete="off"></td>
                                                <td><input type="text" class="form-control material_prices text-end" name="material_prices[]" value="<?= htmlspecialchars($materialPrices) ?>" readonly></td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn-delete-row" title="ลบรายการนี้">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php }
                                            } catch (PDOException $e) {
                                                echo "<tr><td colspan='7'><div class='alert alert-danger m-0'>Error: " . $e->getMessage() . "</div></td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr id="material_sum_show">
                                                <td colspan="5" class="text-end fw-bold" style="color: var(--text-dark);">
                                                    <i class="fas fa-calculator me-2 text-primary"></i> รวมเป็นเงิน
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="procurement_paid" name="procurement_paid"
                                                           value="<?= htmlspecialchars($procurement_paid ?? '') ?>" readonly>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <button type="button" id="btn_add_material" class="btn btn-modern btn-modern-success">
                                    <i class="fas fa-plus"></i> เพิ่มรายการ
                                </button>
                                <div class="tip-box">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>เคล็ดลับ:</strong> กดปุ่ม "เพิ่มรายการ" เพื่อเพิ่มแถวรายการพัสดุได้ไม่จำกัด
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-end align-items-stretch align-items-md-center pt-3" style="border-top: 1px solid var(--border-soft);">
                            <a href="procurement_show.php?act_id=<?= $act_id ?>" class="btn btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i> ยกเลิก
                            </a>
                            <button type="submit" name="save_and_add_new" value="1" class="btn btn-modern btn-modern-warning" onclick="return CheckPaid()">
                                <i class="fas fa-plus-circle"></i> บันทึกและเพิ่มบิลใหม่ (ร้านอื่น)
                            </button>
                            <button type="submit" class="btn btn-modern btn-modern-primary" onclick="return CheckPaid()">
                                <i class="fas fa-save"></i> บันทึกและกลับไปหน้ารายการ
                            </button>
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                ใช้ปุ่ม <strong>"บันทึกและเพิ่มบิลใหม่ (ร้านอื่น)"</strong> เพื่อบันทึกหลายบิลโดยไม่ต้องกลับไปหน้ารายการ
                            </small>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<!-- Scripts -->
<script>
$(function () {
    // Cleave for total
    new Cleave('#procurement_paid', { numeral: true, numeralPositiveOnly: true, numeralThousandsGroupStyle: 'thousand' });

    // Select2
    if ($('#inspector_position_id').length) {
        $('#inspector_position_id').select2({ placeholder: '-- โปรดเลือก --', minimumResultsForSearch: Infinity });
    }
    if ($('#expense_id').length) {
        $('#expense_id').select2({ placeholder: '-- เลือกประเภท --', minimumResultsForSearch: Infinity });
    }
    if ($('#payee_id').length) {
        $('#payee_id').select2({ placeholder: '-- เลือกผู้ขาย/ร้านค้า --' });
    }

    // Initialize all existing material rows (Cleave + events)
    $('#material_tbody .material_list_show').each(function () {
        initMaterialRow($(this));
    });

    // Delegated events for dynamic rows
    $(document).on('input', '.material_amount, .material_price', function () {
        sumTotalPrice();
        updateMaterialCount();
    });

    $(document).on('click', '.btn-delete-row', function () {
        if ($('#material_tbody .material_list_show').length <= 1) {
            alert('ต้องมีอย่างน้อย 1 รายการ');
            return;
        }
        $(this).closest('tr').remove();
        renumberRows();
        sumTotalPrice();
        updateMaterialCount();
    });

    // Add row button
    $('#btn_add_material').on('click', function () {
        addMaterialRow();
    });

    // Set exact number of material rows
    $('#btn_set_material_count').on('click', function () {
        const target = parseInt($('#material_row_count').val()) || 0;
        setMaterialRowCount(target);
    });

    // Allow pressing Enter in the count input
    $('#material_row_count').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn_set_material_count').click();
        }
    });

    // Initial calculation
    sumTotalPrice();
    updateMaterialCount();
    renumberRows();

    const initialCount = $('#material_tbody .material_list_show').length;
    $('#material_row_count').val(initialCount);
});

function initMaterialRow($row) {
    $row.find('.material_price, .material_prices').each(function () {
        new Cleave(this, { numeral: true, numeralPositiveOnly: true, numeralThousandsGroupStyle: 'thousand' });
    });
}

function addMaterialRow(autoFocus = true) {
    const $tbody = $('#material_tbody');
    const newRowHtml = `
        <tr class="material_list_show">
            <td class="text-center align-middle">
                <span class="row-num"></span>
            </td>
            <td><input type="text" class="form-control material_name" name="material_name[]" value="" placeholder="ชื่อรายการพัสดุ" autocomplete="off"></td>
            <td><input type="text" class="form-control material_amount text-center" name="material_amount[]" value="" maxlength="5" autocomplete="off"></td>
            <td><input type="text" class="form-control material_unit text-center" name="material_unit[]" value="" maxlength="20" placeholder="เช่น ชิ้น" autocomplete="off"></td>
            <td><input type="text" class="form-control material_price text-end" name="material_price[]" value="" maxlength="12" autocomplete="off"></td>
            <td><input type="text" class="form-control material_prices text-end" name="material_prices[]" value="" readonly></td>
            <td class="text-center align-middle">
                <button type="button" class="btn-delete-row" title="ลบรายการนี้">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    $tbody.append(newRowHtml);
    const $newRow = $tbody.find('tr.material_list_show').last();
    initMaterialRow($newRow);
    renumberRows();
    sumTotalPrice();
    updateMaterialCount();

    if (autoFocus) {
        $newRow.find('.material_name').focus();
    }
}

function setMaterialRowCount(targetCount) {
    const $tbody = $('#material_tbody');
    let currentCount = $tbody.find('tr.material_list_show').length;

    if (targetCount < 0) targetCount = 0;
    if (targetCount > 100) targetCount = 100;

    if (targetCount === currentCount) return;

    if (targetCount > currentCount) {
        const toAdd = targetCount - currentCount;
        for (let i = 0; i < toAdd; i++) {
            addMaterialRow(false);
        }
    } else {
        const toRemove = currentCount - targetCount;
        for (let i = 0; i < toRemove; i++) {
            const $lastRow = $tbody.find('tr.material_list_show').last();
            if ($lastRow.length === 0) break;
            $lastRow.remove();
        }
    }

    renumberRows();
    sumTotalPrice();
    updateMaterialCount();
}

function renumberRows() {
    $('#material_tbody .material_list_show').each(function (idx) {
        $(this).find('.row-num').text(idx + 1);
    });
}

function sumTotalPrice() {
    let total_sum = 0;
    $('#material_tbody .material_list_show').each(function () {
        const amt = Number(($(this).find('.material_amount').val() || '').replace(/[^0-9.]/g, '')) || 0;
        const prc = Number(($(this).find('.material_price').val()  || '').replace(/[^0-9.]/g, '')) || 0;
        const line = amt * prc;
        $(this).find('.material_prices').val(line.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        total_sum += line;
    });
    $('#procurement_paid').val(total_sum.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
}

function updateMaterialCount() {
    const count = $('#material_tbody .material_list_show').length;
    $('#material_row_count').val(count);
}

function CheckPaid() {
    const paid = Number(($('#procurement_paid').val() || '').replace(/[^0-9.]/g, '')) || 0;
    if (paid <= 0) {
        alert('⚠️ กรุณากรอกรายการพัสดุอย่างน้อย 1 รายการ');
        return false;
    }
    updateMaterialCount();
    return true;
}
</script>
</body>
</html>
<?php } ?>