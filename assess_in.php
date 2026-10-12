<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
}

include "./includes/connect.php";
include "./includes/function.php";

$act_id = $_GET['act_id'] ?? null;

if (empty($act_id) || !is_numeric($act_id)) {
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
    exit;
}

try {
    $sql = "SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':act_id' => $act_id, ':school_id' => $_SESSION['ss_school_id']]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activity) {
        echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// ตัวแปรลิงก์ — ใช้ตัวแปร PHP เพื่อกัน tag หาย
$link_home    = "index.php";
$link_main    = "assess_main.php";
$link_show    = "assess_show.php?act_id=" . $act_id;
$font_url     = "https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>บันทึกคะแนน - ระบบการศึกษาต่อเนื่อง</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?php echo $font_url; ?>" rel="stylesheet">

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

        .doc-hero {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #4f46e5 100%);
            border-radius: var(--radius-lg);
            padding: 1.75rem 2.25rem;
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
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .hero-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
            max-width: 700px;
        }
        .hero-subtitle {
            font-size: 0.9rem;
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
            font-size: 0.78rem;
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
            padding: 0.55rem 1rem;
            color: white;
            text-align: center;
            min-width: 90px;
        }
        .hero-stat-value {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.72rem;
            opacity: 0.9;
            margin-top: 0.2rem;
        }

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
        .section-icon.amber  { background: linear-gradient(135deg, #f59e0b, #ef4444); }
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 0;
        }
        .info-item {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .info-item.full { grid-column: 1 / -1; }
        .info-item-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--bg-soft);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            flex-shrink: 0;
        }
        .info-item-icon.indigo { background: var(--primary-light); color: var(--primary); }
        .info-item-icon.cyan   { background: #ecfeff; color: var(--info); }
        .info-item-icon.amber  { background: #fffbeb; color: var(--warning); }
        .info-label {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 600;
            margin: 0 0 0.15rem 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            font-size: 0.92rem;
            color: var(--text-dark);
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
        }

        .criteria-card {
            margin: 1.25rem;
            padding: 1.1rem 1.25rem;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid #fde68a;
            color: #78350f;
        }
        .criteria-list {
            list-style: none;
            padding: 0;
            margin: 0.5rem 0 0 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 0.5rem;
        }
        .criteria-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.6);
            padding: 0.5rem 0.85rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
        }
        .criteria-list .badge-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #f59e0b;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .score-table {
            margin: 0 !important;
            border-collapse: separate;
            border-spacing: 0;
        }
        .score-table thead th {
            background: #f8fafc !important;
            color: var(--text-dark) !important;
            font-weight: 700 !important;
            border: none !important;
            border-bottom: 2px solid var(--border-soft) !important;
            padding: 1rem 0.75rem !important;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
            vertical-align: middle;
        }
        .score-table thead th .max-pts {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 0.15rem;
            text-transform: none;
            letter-spacing: 0;
        }
        .score-table tbody td {
            padding: 0.75rem 0.75rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle;
            font-size: 0.92rem;
        }
        .score-table tbody tr {
            transition: all 0.2s ease;
        }
        .score-table tbody tr:hover {
            background-color: #f0fdfa !important;
        }
        .score-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .row-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--bg-soft);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.82rem;
        }

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

        .score-input-wrap {
            display: flex;
            justify-content: center;
        }
        .form-score-input {
            width: 75px;
            height: 44px;
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            background: white;
            color: var(--text-dark);
            transition: all 0.2s ease;
        }
        .form-score-input:focus {
            outline: none;
            border-color: var(--success);
            box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
        }
        .form-score-input.invalid {
            border-color: var(--danger);
            color: var(--danger);
            background: #fef2f2;
        }
        .form-total-input {
            width: 90px;
            height: 44px;
            text-align: center;
            font-weight: 800;
            font-size: 1.05rem;
            border: 2px solid var(--border-soft);
            border-radius: 10px;
            background: var(--bg-soft);
            color: var(--text-dark);
            cursor: not-allowed;
        }
        .form-total-input.pass {
            border-color: var(--success);
            background: linear-gradient(135deg, #ecfdf5, #ecfeff);
            color: var(--success);
        }
        .form-total-input.fail {
            border-color: var(--danger);
            background: #fef2f2;
            color: var(--danger);
        }

        .action-bar {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 2px solid var(--border-soft);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 -4px 16px rgba(15,23,42,0.06);
            border-bottom-left-radius: var(--radius-md);
            border-bottom-right-radius: var(--radius-md);
            z-index: 10;
        }
        .action-bar-info {
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .action-bar-info strong { color: var(--text-dark); }
        .action-buttons {
            display: flex;
            gap: 0.6rem;
        }
        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            border: none;
            transition: all 0.25s ease;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-save {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: white;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16,185,129,0.4);
            color: white;
        }
        .btn-cancel {
            background: var(--bg-soft);
            color: var(--text-muted);
            border: 1.5px solid var(--border-soft);
        }
        .btn-cancel:hover {
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .app-main > .app-footer {
            margin-left: 0 !important;
            width: 100% !important;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .doc-hero { padding: 1.25rem; }
            .hero-title { font-size: 1.15rem; }
            .hero-avatar { width: 52px; height: 52px; font-size: 1.4rem; }
            .form-score-input { width: 60px; height: 40px; font-size: 0.9rem; }
            .form-total-input { width: 75px; height: 40px; font-size: 0.95rem; }
            .info-item { border-right: none; }
            .action-bar { flex-direction: column; align-items: stretch; }
            .action-buttons { justify-content: center; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .section-card { animation: fadeInUp 0.4s ease-out; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            $row = null;
            $date = '';
            try {
                $sql = "SELECT activity.activity_id, activity.activity_name,
                            course.course_name, course.course_hour,
                            activity.date_id, activity.date_range,
                            activity.date_start, activity.date_end,
                            activity.time_start, activity.time_end,
                            activity.total_study, activity.total_graduate,
                            activity.status_line, activity.status_assess
                        FROM activity
                        INNER JOIN course ON activity.course_id = course.course_id
                        WHERE activity.activity_id = :act_id AND activity.school_id = :school_id
                        LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':act_id' => $act_id, ':school_id' => $_SESSION['ss_school_id']]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    if ($row['date_id'] == "1") {
                        if ($row['date_start'] == $row['date_end']) {
                            $date = ADYearToBEYearTH($row['date_start'], 2);
                        } else {
                            $date = ADYearToBEYearTH($row['date_start'], 2) . " ถึง " . ADYearToBEYearTH($row['date_end'], 2);
                        }
                    } else {
                        $date = DateRangeToString($row['date_range'], 2);
                    }
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            $total_study    = (int)($row['total_study']    ?? 0);
            $total_graduate = (int)($row['total_graduate'] ?? 0);
            $total_fail     = max(0, $total_study - $total_graduate);
            $is_assessed    = ($row && $row['status_assess'] == "1");
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="<?php echo $link_home; ?>"><i class="fas fa-home me-1"></i>หน้าหลัก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo $link_main; ?>">การประเมินผลฯ</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo $link_show; ?>">รายละเอียดการประเมินผล</a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold">บันทึกคะแนน</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                        <div class="hero-avatar">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div style="min-width: 0;">
                            <span class="mode-badge">
                                <i class="fas fa-pen-to-square"></i>
                                บันทึกคะแนนผู้เรียน
                            </span>
                            <h1 class="hero-title mt-2">
                                <?php echo htmlspecialchars($row['activity_name'] ?? 'ไม่พบโครงการ'); ?>
                            </h1>
                            <p class="hero-subtitle">
                                <i class="fas fa-calendar-alt me-1"></i> <?php echo $date; ?>
                                &nbsp;•&nbsp;
                                <i class="far fa-clock me-1"></i>
                                <?php echo $row['time_start'] ?? '--'; ?> -
                                <?php echo $row['time_end'] ?? '--'; ?> น.
                            </p>
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($total_study); ?></div>
                            <div class="hero-stat-label">ทั้งหมด</div>
                        </div>
                        <?php if ($is_assessed) { ?>
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($total_graduate); ?></div>
                            <div class="hero-stat-label">ผ่าน</div>
                        </div>
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo number_format($total_fail); ?></div>
                            <div class="hero-stat-label">ไม่ผ่าน</div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- รายละเอียดข้อมูลโครงการ -->
            <?php if ($row) { ?>
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-title-wrap">
                        <div class="section-icon purple"><i class="fas fa-info-circle"></i></div>
                        <div class="section-title-text">
                            <h6>รายละเอียดข้อมูลโครงการ</h6>
                            <p>ข้อมูลหลักสูตรและกำหนดการ</p>
                        </div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-item-icon indigo"><i class="fas fa-book"></i></div>
                        <div>
                            <p class="info-label">หลักสูตร</p>
                            <p class="info-value">
                                <?php echo htmlspecialchars($row['course_name']); ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                                    <?php echo htmlspecialchars($row['course_hour']); ?> ชั่วโมง
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fas fa-calendar-day"></i></div>
                        <div>
                            <p class="info-label">วันที่ดำเนินการ</p>
                            <p class="info-value"><?php echo $date; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon amber"><i class="far fa-clock"></i></div>
                        <div>
                            <p class="info-label">เวลาดำเนินการ</p>
                            <p class="info-value">
                                <?php echo htmlspecialchars($row['time_start']); ?> น. ถึง
                                <?php echo htmlspecialchars($row['time_end']); ?> น.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <form name="form1" id="form1" action="assess_save.php" method="post">
                <input type="hidden" name="act_id" value="<?php echo $act_id; ?>">

                <!-- เกณฑ์การประเมิน -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-title-wrap">
                            <div class="section-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="section-title-text">
                                <h6>เกณฑ์การจบหลักสูตร</h6>
                                <p>ต้องได้คะแนนรวมไม่น้อยกว่าร้อยละ 60 และคะแนน "ความรู้" ไม่ต่ำกว่า 12</p>
                            </div>
                        </div>
                    </div>
                    <div class="criteria-card">
                        <ul class="criteria-list">
                            <li>
                                <span class="badge-num">1</span>
                                <span><strong>ความรู้ความเข้าใจ</strong> เต็ม 20 (ต้องได้ ≥ 12)</span>
                            </li>
                            <li>
                                <span class="badge-num">2</span>
                                <span><strong>ทักษะการปฏิบัติ</strong> เต็ม 40 คะแนน</span>
                            </li>
                            <li>
                                <span class="badge-num">3</span>
                                <span><strong>คุณภาพของผลงาน</strong> เต็ม 40 คะแนน</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ตารางบันทึกคะแนน -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-title-wrap">
                            <div class="section-icon"><i class="fas fa-table"></i></div>
                            <div class="section-title-text">
                                <h6>กรอกคะแนนผู้เรียน</h6>
                                <p>กรอกคะแนนแต่ละด้าน • ระบบคำนวณคะแนนรวมและสถานะให้อัตโนมัติ</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="scoreTable" class="table score-table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ลำดับ</th>
                                    <th style="text-align: left;">ชื่อ-สกุล</th>
                                    <th style="width: 120px;">
                                        ความรู้
                                        <span class="max-pts">เต็ม 20</span>
                                    </th>
                                    <th style="width: 120px;">
                                        ทักษะ
                                        <span class="max-pts">เต็ม 40</span>
                                    </th>
                                    <th style="width: 120px;">
                                        ผลงาน
                                        <span class="max-pts">เต็ม 40</span>
                                    </th>
                                    <th style="width: 140px;">
                                        คะแนนรวม
                                        <span class="max-pts">เต็ม 100</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $sql = "SELECT student.student_id, student.firstname,
                                                student.lastname, train.student_id,
                                                train.a_score, train.b_score,
                                                train.c_score, train.total_scores
                                            FROM train
                                            INNER JOIN student ON train.student_id = student.student_id
                                            WHERE train.activity_id = :act_id AND student.school_id = :school_id
                                            ORDER BY student.student_id DESC";

                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([':act_id' => $act_id, ':school_id' => $_SESSION['ss_school_id']]);
                                    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($students) {
                                        $a = 0;
                                        foreach ($students as $sRow) {
                                            if (!empty($sRow['firstname'])) {
                                                $a++;
                                                $firstChar = mb_substr($sRow['firstname'], 0, 1, 'UTF-8');
                                ?>
                                <tr>
                                    <input type="hidden" name="student_id[]" value="<?php echo $sRow['student_id']; ?>">
                                    <td class="text-center">
                                        <span class="row-number"><?php echo $a; ?></span>
                                    </td>
                                    <td>
                                        <div class="student-name">
                                            <div class="student-avatar"><?php echo $firstChar; ?></div>
                                            <div class="student-text">
                                                <?php echo htmlspecialchars($sRow['firstname']); ?>
                                                <?php echo htmlspecialchars($sRow['lastname']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="score-input-wrap">
                                            <input type="text" class="form-score-input" id="a_score"
                                                   name="a_score[]" autocomplete="off" maxlength="2"
                                                   value="<?php echo htmlspecialchars($sRow['a_score']); ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="score-input-wrap">
                                            <input type="text" class="form-score-input" id="b_score"
                                                   name="b_score[]" autocomplete="off" maxlength="2"
                                                   value="<?php echo htmlspecialchars($sRow['b_score']); ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="score-input-wrap">
                                            <input type="text" class="form-score-input" id="c_score"
                                                   name="c_score[]" autocomplete="off" maxlength="2"
                                                   value="<?php echo htmlspecialchars($sRow['c_score']); ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="score-input-wrap">
                                            <input type="text" class="form-total-input"
                                                   id="total_scores" name="total_scores[]"
                                                   value="<?php echo htmlspecialchars($sRow['total_scores']); ?>" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'>";
                                        echo "<i class='fas fa-user-slash fa-2x mb-2 d-block opacity-50'></i>";
                                        echo "ยังไม่มีผู้เข้าร่วมโครงการ";
                                        echo "</td></tr>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<tr><td colspan='6' class='text-center text-danger py-4'>";
                                    echo "<i class='fas fa-exclamation-triangle me-2'></i>";
                                    echo "Error: " . htmlspecialchars($e->getMessage());
                                    echo "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sticky Action Bar -->
                    <div class="action-bar">
                        <div class="action-bar-info">
                            <span>
                                <i class="fas fa-users text-success me-1"></i>
                                ผู้เรียนทั้งหมด: <strong><?php echo number_format($total_study); ?></strong> คน
                            </span>
                            <span id="liveStat">
                                <i class="fas fa-graduation-cap text-info me-1"></i>
                                ผ่านเกณฑ์ (Live): <strong id="livePass">0</strong> คน
                            </span>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn-modern btn-cancel"
                                    onclick="window.location='<?php echo $link_show; ?>';return false;">
                                <i class="fas fa-times"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn-modern btn-save">
                                <i class="fas fa-save"></i> บันทึกคะแนน
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script>
    window.onload = function() {
        ShowAssess();
    };

    $(document).ready(function() {
        $("input[id$='_score']").on('input', function(){
            sumTotalScore();
        });
    });

    function ShowAssess() {
        var f = document.getElementById('form1');
        if (!f || !f['a_score'] || !f['a_score'].length) return;

        var passCount = 0;
        for (var i = 0; i < f['a_score'].length; i++) {
            var a_score = f['a_score'][i];
            var total_scores = f['total_scores'][i];

            if (Number(total_scores.value) > 59 && Number(total_scores.value) < 101 && Number(a_score.value) >= 12) {
                total_scores.classList.add('pass');
                total_scores.classList.remove('fail');
                passCount++;
            } else {
                total_scores.classList.add('fail');
                total_scores.classList.remove('pass');
            }
        }
        var livePass = document.getElementById('livePass');
        if (livePass) livePass.textContent = passCount;
    }

    function sumTotalScore() {
        var f = document.getElementById('form1');
        if (!f || !f['a_score'] || !f['a_score'].length) return;

        var passCount = 0;
        for (var i = 0; i < f['a_score'].length; i++) {
            var a_score = f['a_score'][i];
            var b_score = f['b_score'][i];
            var c_score = f['c_score'][i];
            var total_scores = f['total_scores'][i];

            a_score.value = a_score.value.replace(/[^0-9]/g, "");
            b_score.value = b_score.value.replace(/[^0-9]/g, "");
            c_score.value = c_score.value.replace(/[^0-9]/g, "");

            if (Number(a_score.value) > 20) {
                a_score.classList.add('invalid');
                a_score.value = '20';
            } else {
                a_score.classList.remove('invalid');
            }
            if (Number(b_score.value) > 40) {
                b_score.classList.add('invalid');
                b_score.value = '40';
            } else {
                b_score.classList.remove('invalid');
            }
            if (Number(c_score.value) > 40) {
                c_score.classList.add('invalid');
                c_score.value = '40';
            } else {
                c_score.classList.remove('invalid');
            }

            total_scores.value = Number(a_score.value) + Number(b_score.value) + Number(c_score.value);

            if (Number(total_scores.value) > 59 && Number(total_scores.value) < 101 && Number(a_score.value) >= 12) {
                total_scores.classList.add('pass');
                total_scores.classList.remove('fail');
                passCount++;
            } else {
                total_scores.classList.add('fail');
                total_scores.classList.remove('pass');
            }
        }

        var livePass = document.getElementById('livePass');
        if (livePass) livePass.textContent = passCount;
    }
</script>
</body>
</html>