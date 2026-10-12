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
include "./modules/head.inc.php";

$act_id = isset($_REQUEST['act_id']) ? $_REQUEST['act_id'] : null;
$school_id = $_SESSION['ss_school_id'];

if (!is_numeric($act_id)) {
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1");
    $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
    $activity = $stmt->fetch();

    if (!$activity) {
        echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM reporting WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1");
    $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
    $reporting = $stmt->fetch();

    $reporting_id = isset($reporting['reporting_id']) ? $reporting['reporting_id'] : '';

    for ($i = 1; $i <= 4; $i++) {
        ${"photo_material_$i"}      = isset($reporting["photo_material_$i"]) ? $reporting["photo_material_$i"] : '';
        ${"photo_material_{$i}_text"} = isset($reporting["photo_material_{$i}_text"]) ? $reporting["photo_material_{$i}_text"] : '';
    }
    for ($i = 1; $i <= 6; $i++) {
        ${"photo_activity_$i"}      = isset($reporting["photo_activity_$i"]) ? $reporting["photo_activity_$i"] : '';
        ${"photo_activity_{$i}_text"} = isset($reporting["photo_activity_{$i}_text"]) ? $reporting["photo_activity_{$i}_text"] : '';
    }

} catch (PDOException $e) {
    error_log("reporting_in.php Error: " . $e->getMessage());
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
    exit();
}

$font_url = "https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap";
$link_home = "index.php";
$link_main = "reporting_main.php";

// นับจำนวนภาพที่อัปโหลดแล้ว
$count_material = 0;
$count_activity = 0;
for ($i = 1; $i <= 4; $i++) {
    if (!empty(${"photo_material_$i"})) $count_material++;
}
for ($i = 1; $i <= 6; $i++) {
    if (!empty(${"photo_activity_$i"})) $count_activity++;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <title>บันทึกภาพ - ระบบการศึกษาต่อเนื่อง</title>

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

        /* ===== Hero Header ===== */
        .doc-hero {
            background: linear-gradient(135deg, #06b6d4 0%, #4f46e5 50%, #7c3aed 100%);
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
            width: 64px; height: 64px;
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
            background: linear-gradient(to right, #ecfeff, white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .section-card.cat-material .section-card-header {
            background: linear-gradient(to right, #fef2f2, white);
        }
        .section-card.cat-activity .section-card-header {
            background: linear-gradient(to right, #ecfeff, white);
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
            background: linear-gradient(135deg, #06b6d4, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.05rem;
        }
        .section-icon.purple { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .section-icon.danger { background: linear-gradient(135deg, #ef4444, #ec4899); }
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
        .section-progress {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .section-progress.material {
            background: #fef2f2;
            color: var(--danger);
        }
        .section-progress.activity {
            background: #ecfeff;
            color: var(--info);
        }

        /* ===== Info Grid ===== */
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
        .info-item-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: var(--bg-soft);
            color: var(--info);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            flex-shrink: 0;
        }
        .info-item-icon.indigo { background: var(--primary-light); color: var(--primary); }
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

        /* ===== Photo Card ===== */
        .photo-grid {
            padding: 1.5rem;
        }
        .photo-card {
            background: var(--bg-soft);
            border: 1.5px solid var(--border-soft);
            border-radius: var(--radius-md);
            padding: 14px;
            transition: all 0.25s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .photo-card:hover {
            border-color: var(--info);
            box-shadow: 0 6px 18px rgba(6,182,212,0.12);
            transform: translateY(-2px);
        }
        .photo-card.has-image {
            background: white;
            border-color: var(--success);
        }
        .photo-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .photo-num {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        .photo-num-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px; height: 24px;
            border-radius: 50%;
            background: var(--bg-soft);
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 700;
        }
        .photo-card.has-image .photo-num-badge {
            background: var(--success);
            color: white;
        }
        .photo-status {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
        }
        .photo-status.empty {
            background: #fffbeb;
            color: var(--warning);
        }
        .photo-status.filled {
            background: #ecfdf5;
            color: var(--success);
        }

        .photo-preview {
            background: white;
            border: 2px dashed var(--border-soft);
            border-radius: 10px;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            overflow: hidden;
            position: relative;
        }
        .photo-card.has-image .photo-preview {
            border: 2px solid var(--success);
            border-style: solid;
        }
        .photo-preview img {
            max-height: 150px;
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        .photo-empty {
            text-align: center;
            color: var(--text-muted);
            padding: 1rem;
        }
        .photo-empty i {
            font-size: 2rem;
            opacity: 0.4;
            display: block;
            margin-bottom: 0.4rem;
        }
        .photo-empty span {
            font-size: 0.82rem;
            font-weight: 500;
        }

        /* File Input */
        .form-file-modern {
            font-size: 0.85rem;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.5rem 0.7rem;
            background: white;
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
            margin-bottom: 8px;
        }
        .form-file-modern:focus {
            outline: none;
            border-color: var(--info);
            box-shadow: 0 0 0 4px rgba(6,182,212,0.12);
        }
        .form-file-modern::file-selector-button {
            border: none;
            background: linear-gradient(135deg, #06b6d4, #4f46e5);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.82rem;
            cursor: pointer;
            margin-right: 0.6rem;
            transition: all 0.2s ease;
        }
        .form-file-modern::file-selector-button:hover {
            opacity: 0.9;
        }

        /* Text Input */
        .form-text-modern {
            width: 100%;
            font-size: 0.88rem;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.5rem 0.85rem;
            background: white;
            transition: all 0.2s ease;
        }
        .form-text-modern:focus {
            outline: none;
            border-color: var(--info);
            box-shadow: 0 0 0 4px rgba(6,182,212,0.12);
        }

        /* ===== Action Bar ===== */
        .action-bar {
            position: sticky;
            bottom: 0;
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-soft);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 -4px 16px rgba(15,23,42,0.06);
            margin-bottom: 1rem;
            z-index: 10;
        }
        .action-bar-info {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.88rem;
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
            background: linear-gradient(135deg, #06b6d4, #4f46e5);
            color: white;
            box-shadow: 0 4px 12px rgba(6,182,212,0.3);
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6,182,212,0.4);
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

        /* ===== Alert Tip ===== */
        .tip-card {
            margin: 1rem 1.5rem 0 1.5rem;
            padding: 0.85rem 1.1rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #ecfeff, #eef2ff);
            border: 1px solid #a5f3fc;
            color: #155e75;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
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
            $activity_id = $act_id;
            try {
                $sql = "SELECT activity.activity_id, activity.activity_name,
                            course.course_name, course.course_hour,
                            activity.date_id, activity.date_range,
                            activity.date_start, activity.date_end,
                            activity.time_start, activity.time_end
                        FROM activity
                        INNER JOIN course ON activity.course_id = course.course_id
                        WHERE activity.activity_id = :act_id AND activity.school_id = :school_id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['act_id' => $act_id, 'school_id' => $school_id]);
                $row = $stmt->fetch();
                if ($row) {
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
                }
            } catch (PDOException $e) {
                error_log("reporting_in.php detail query Error: " . $e->getMessage());
            }
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= $link_home ?>"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="<?= $link_main ?>">รายงานภาพการจัดฯ</a></li>
                    <li class="breadcrumb-item active fw-semibold">บันทึกภาพ</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                        <div class="hero-avatar">
                            <i class="fas fa-camera-retro"></i>
                        </div>
                        <div style="min-width: 0;">
                            <span class="mode-badge">
                                <i class="fas fa-cloud-upload-alt"></i>
                                บันทึกภาพกิจกรรม
                            </span>
                            <h1 class="hero-title mt-2">
                                <?php echo htmlspecialchars(($row ? $row['activity_name'] : 'ไม่พบโครงการ')); ?>
                            </h1>
                            <p class="hero-subtitle">
                                <i class="fas fa-calendar-alt me-1"></i> <?php echo $date; ?>
                                &nbsp;•&nbsp;
                                <i class="far fa-clock me-1"></i>
                                <?php echo ($row ? $row['time_start'] : '--'); ?> -
                                <?php echo ($row ? $row['time_end'] : '--'); ?> น.
                            </p>
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo $count_material; ?>/4</div>
                            <div class="hero-stat-label">ภาพวัสดุ</div>
                        </div>
                        <div class="hero-stat-pill">
                            <div class="hero-stat-value"><?php echo $count_activity; ?>/6</div>
                            <div class="hero-stat-label">ภาพกิจกรรม</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- รายละเอียดโครงการ -->
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
                    <div class="info-item">
                        <div class="info-item-icon indigo"><i class="fas fa-book"></i></div>
                        <div>
                            <p class="info-label">หลักสูตร</p>
                            <p class="info-value">
                                <?php echo htmlspecialchars($row['course_name']); ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
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

            <form action="reporting_save.php" method="POST" enctype="multipart/form-data" name="form1" id="form1">
                <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">
                <input type="hidden" name="reporting_id" value="<?php echo $reporting_id; ?>">

                <!-- Tip -->
                <div class="section-card">
                    <div class="tip-card">
                        <i class="fas fa-lightbulb"></i>
                        <span>
                            <strong>เคล็ดลับ:</strong>
                            เมื่อเลือกไฟล์ภาพ ระบบจะ <strong>บันทึกอัตโนมัติ</strong> ทันที •
                            รองรับ JPG, PNG, GIF •
                            ใส่คำบรรยายภาพได้ก่อนบันทึก
                        </span>
                    </div>
                </div>

                <!-- ภาพวัสดุ -->
                <div class="section-card cat-material">
                    <div class="section-card-header">
                        <div class="section-title-wrap">
                            <div class="section-icon danger"><i class="fas fa-boxes"></i></div>
                            <div class="section-title-text">
                                <h6>ภาพวัสดุ</h6>
                                <p>อัปโหลดภาพถ่ายวัสดุ/อุปกรณ์ที่ใช้ในโครงการ (สูงสุด 4 ภาพ)</p>
                            </div>
                        </div>
                        <span class="section-progress material">
                            <i class="fas fa-images"></i>
                            <?php echo $count_material; ?> / 4
                        </span>
                    </div>

                    <div class="row g-3 photo-grid">
                        <?php for ($i = 1; $i <= 4; $i++):
                            $photo = ${"photo_material_$i"};
                            $photo_text = ${"photo_material_{$i}_text"};
                            $has_image = !empty($photo);
                        ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="photo-card <?php echo $has_image ? 'has-image' : ''; ?>">
                                <div class="photo-card-header">
                                    <span class="photo-num">
                                        <span class="photo-num-badge"><?php echo $i; ?></span>
                                        ภาพที่ <?php echo $i; ?>
                                    </span>
                                    <?php if ($has_image): ?>
                                        <span class="photo-status filled">
                                            <i class="fas fa-check"></i> มีภาพ
                                        </span>
                                    <?php else: ?>
                                        <span class="photo-status empty">
                                            <i class="fas fa-plus"></i> ว่าง
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="photo-preview">
                                    <?php if ($has_image): ?>
                                        <img src="./pictures/material/<?php echo $photo; ?>?<?php echo time(); ?>"
                                             alt="ภาพวัสดุ <?php echo $i; ?>">
                                    <?php else: ?>
                                        <div class="photo-empty">
                                            <i class="fas fa-image"></i>
                                            <span>ยังไม่มีภาพ</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-file-modern"
                                       id="material<?php echo $i; ?>" name="material<?php echo $i; ?>"
                                       onchange="SubmitForm()" accept=".jpeg,.jpg,.png,.gif">
                                <input type="text" class="form-text-modern"
                                       id="material<?php echo $i; ?>_text" name="material<?php echo $i; ?>_text"
                                       placeholder="✏️ คำบรรยายภาพ..."
                                       value="<?php echo htmlspecialchars($photo_text); ?>">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- ภาพกิจกรรม -->
                <div class="section-card cat-activity">
                    <div class="section-card-header">
                        <div class="section-title-wrap">
                            <div class="section-icon"><i class="fas fa-camera"></i></div>
                            <div class="section-title-text">
                                <h6>ภาพกิจกรรม</h6>
                                <p>อัปโหลดภาพบรรยากาศการจัดกิจกรรม (สูงสุด 6 ภาพ)</p>
                            </div>
                        </div>
                        <span class="section-progress activity">
                            <i class="fas fa-images"></i>
                            <?php echo $count_activity; ?> / 6
                        </span>
                    </div>

                    <div class="row g-3 photo-grid">
                        <?php for ($i = 1; $i <= 6; $i++):
                            $photo = ${"photo_activity_$i"};
                            $photo_text = ${"photo_activity_{$i}_text"};
                            $has_image = !empty($photo);
                        ?>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                            <div class="photo-card <?php echo $has_image ? 'has-image' : ''; ?>">
                                <div class="photo-card-header">
                                    <span class="photo-num">
                                        <span class="photo-num-badge"><?php echo $i; ?></span>
                                        ภาพที่ <?php echo $i; ?>
                                    </span>
                                    <?php if ($has_image): ?>
                                        <span class="photo-status filled">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="photo-status empty">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="photo-preview">
                                    <?php if ($has_image): ?>
                                        <img src="./pictures/activity/<?php echo $photo; ?>?<?php echo time(); ?>"
                                             alt="ภาพกิจกรรม <?php echo $i; ?>">
                                    <?php else: ?>
                                        <div class="photo-empty">
                                            <i class="fas fa-image"></i>
                                            <span>ยังไม่มีภาพ</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-file-modern"
                                       id="activity<?php echo $i; ?>" name="activity<?php echo $i; ?>"
                                       onchange="SubmitForm()" accept=".jpeg,.jpg,.png,.gif">
                                <input type="text" class="form-text-modern"
                                       id="activity<?php echo $i; ?>_text" name="activity<?php echo $i; ?>_text"
                                       placeholder="✏️ คำบรรยาย..."
                                       value="<?php echo htmlspecialchars($photo_text); ?>">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Sticky Action Bar -->
                <div class="action-bar">
                    <div class="action-bar-info">
                        <span>
                            <i class="fas fa-boxes text-danger me-1"></i>
                            ภาพวัสดุ: <strong><?php echo $count_material; ?>/4</strong>
                        </span>
                        <span>
                            <i class="fas fa-camera text-info me-1"></i>
                            ภาพกิจกรรม: <strong><?php echo $count_activity; ?>/6</strong>
                        </span>
                        <span>
                            <i class="fas fa-images text-primary me-1"></i>
                            รวม: <strong><?php echo ($count_material + $count_activity); ?>/10</strong>
                        </span>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn-modern btn-cancel"
                                onclick="window.location='<?php echo $link_main; ?>';return false;">
                            <i class="fas fa-times"></i> ยกเลิก
                        </button>
                        <button type="submit" class="btn-modern btn-save">
                            <i class="fas fa-save"></i> บันทึกคำบรรยายทั้งหมด
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script type="text/javascript" src="./plugins/Block-ui/jquery.blockUI.js"></script>

<script type="text/javascript">
    function SubmitForm() {
        $.blockUI({
            message: '<div style="padding: 1.5rem;"><i class="fas fa-spinner fa-spin fa-2x text-white mb-2"></i><br><h5 class="text-white mb-0">กำลังอัปโหลด...</h5><p class="text-white-50 small mb-0 mt-1">โปรดรอสักครู่</p></div>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: 'rgba(0,0,0,0.8)',
                '-webkit-border-radius': '12px',
                '-moz-border-radius': '12px',
                borderRadius: '12px',
                opacity: 1,
                color: '#fff'
            }
        });
        $("#form1").submit();
    }
</script>
</body>
</html>