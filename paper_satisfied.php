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
    <title>ประเมินความพึงพอใจ | C-Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --pink: #ec4899;
            --purple: #7c3aed;
            --teal: #14b8a6;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(15,23,42,0.08);
            --shadow-lg: 0 10px 40px rgba(16,185,129,0.18);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdfa 100%);
            min-height: 100vh;
        }

        /* Protect Font Awesome icons from Sarabun font override */
        .fas, .far, .fab, .fa, i[class*="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }
        .fab { font-weight: 400 !important; }
        .fas, .far { font-weight: 900 !important; }

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
            color: var(--success-dark);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        /* ===== Hero Header (Green/Teal theme) ===== */
        .doc-hero {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
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
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            border-radius: 50%;
        }
        .doc-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
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
            opacity: 0.95;
            margin: 0.25rem 0 0 0;
        }
        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.22);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.35);
        }
        .hero-stat-pill {
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
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
            opacity: 0.95;
            margin-top: 0.2rem;
        }
        .hero-stars {
            display: inline-flex;
            gap: 3px;
            margin-top: 0.4rem;
            font-size: 0.85rem;
            color: #fef3c7;
        }

        /* ===== Info Banner ===== */
        .info-banner {
            background: white;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            border: 1px solid var(--border-soft);
            border-left: 4px solid var(--success);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            animation: fadeInUp 0.4s ease-out;
        }
        .info-banner-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: linear-gradient(135deg, #10b981, #14b8a6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .info-banner-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.2;
        }
        .info-banner-desc {
            font-size: 0.82rem;
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
            background: linear-gradient(to right, #ecfdf5, white);
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
            background: linear-gradient(135deg, #10b981, #14b8a6);
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
            background: #ecfdf5 !important;
            color: var(--text-dark) !important;
            font-weight: 700 !important;
            border: none !important;
            border-bottom: 2px solid #a7f3d0 !important;
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
        .modern-table tbody tr { transition: all 0.2s ease; }
        .modern-table tbody tr:hover { background-color: #f0fdf4 !important; }
        .modern-table tbody tr:last-child td { border-bottom: none !important; }

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
            background: linear-gradient(135deg, #d1fae5, #ccfbf1);
            color: #047857;
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
            background: linear-gradient(135deg, #10b981, #14b8a6);
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
        }
        .action-btn-icon:hover {
            transform: translateY(-2px);
            color: white;
        }
        .ab-success { background: #ecfdf5; color: var(--success-dark); }
        .ab-success:hover { background: var(--success); box-shadow: 0 4px 12px rgba(16,185,129,0.4); }
        .ab-warn { background: #fffbeb; color: #b45309; }
        .ab-warn:hover { background: #f59e0b; color: #fff; box-shadow: 0 4px 12px rgba(245,158,11,0.4); }
        .ab-primary { background: var(--primary-light); color: var(--primary); }
        .ab-primary:hover { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(79,70,229,0.4); }

        /* Star rating display */
        .star-display {
            display: inline-flex;
            gap: 2px;
            color: #fbbf24;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }

        /* ===== DataTables Custom Styling ===== */
        .dataTables_wrapper { padding: 1.25rem 1.5rem; }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.5rem 0.9rem;
            font-size: 0.9rem;
            background: var(--bg-soft);
            min-width: 240px;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--success);
            box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
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
            background: #d1fae5 !important;
            color: #047857 !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #10b981, #14b8a6) !important;
            color: white !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(16,185,129,0.35);
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
                error_log("paper_satisfied.php Count Error: " . $e->getMessage());
            }
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">ประเมินความพึงพอใจ</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="doc-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-clipboard-check"></i>
                                ระบบประเมินผลโครงการ
                            </span>
                            <h1 class="hero-title mt-2">ประเมินความพึงพอใจ</h1>
                            <p class="hero-subtitle">พิมพ์แบบประเมินความพึงพอใจของผู้เข้าร่วมโครงการ/กิจกรรม</p>
                            <div class="hero-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="hero-stat-pill">
                        <div class="hero-stat-value"><?= number_format($totalActivities) ?></div>
                        <div class="hero-stat-label">โครงการทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- Info Banner -->
            <div class="info-banner">
                <div class="info-banner-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <h6 class="info-banner-title">เกี่ยวกับแบบประเมินความพึงพอใจ</h6>
                    <p class="info-banner-desc">แบบประเมินนี้ใช้สำหรับเก็บข้อมูลความคิดเห็นและความพึงพอใจของผู้เข้าร่วมโครงการ เพื่อนำไปพัฒนาปรับปรุงคุณภาพการดำเนินงานในครั้งต่อไป</p>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <div class="table-icon-wrap">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="table-title-text">
                            <h6>รายการโครงการ / กิจกรรม</h6>
                            <p>คลิกที่ไอคอนเพื่อพิมพ์แบบประเมินความพึงพอใจ</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table modern-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ลำดับ</th>
                                <th style="width: 180px;">วันที่</th>
                                <th>ชื่อโครงการ</th>
                                <th style="width: 120px;">แบบประเมินฯ</th>
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
                                    if ($row['date_id'] == "1") {
                                        if ($row['date_start'] == $row['date_end']) {
                                            $date = ADYearToBEYearTH($row['date_start'], 1);
                                        } else {
                                            $date = ADYearToBEYearTH($row['date_start'], 1) . " ถึง " . ADYearToBEYearTH($row['date_end'], 1);
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

                                    echo "<tr>";
                                    echo "<td class='text-center'><span class='row-number'>{$a}</span></td>";
                                    echo "<td class='text-center'><span class='date-badge'><i class='fas fa-calendar-alt'></i> {$date}</span></td>";
                                    echo "<td>
                                            <div class='project-name'>
                                                <div class='project-icon'><i class='fas fa-book'></i></div>
                                                <div>
                                                    <div class='project-text'>{$aname}</div>
                                                    <div class='project-id'>รหัสโครงการ #{$aid}</div>
                                                </div>
                                            </div>
                                          </td>";

                                    // === แบบประเมินฯ ===
                                    echo "<td class='text-center'>";
                                    echo "<div class='action-group'>";
                                    echo "<a class='action-btn-icon ab-success' href='new_report/pt12.php?act_id={$aid}' target='_blank' data-bs-toggle='tooltip' title='แบบประเมินความพึงพอใจ'>";
                                    echo "<i class='fas fa-star'></i>";
                                    echo "</a>";
                                    echo "<a class='action-btn-icon ab-warn' href='new_report/pt20.php?act_id={$aid}' target='_blank' data-bs-toggle='tooltip' title='แบบติดตามผู้ผ่านการฝึกอบรม (พต.20)'>";
                                    echo "<i class='fas fa-user-check'></i>";
                                    echo "</a>";
                                    echo "<a class='action-btn-icon ab-primary' href='satisfied_calc.php?act_id={$aid}' data-bs-toggle='tooltip' title='คำนวณความพึงพอใจ (ร้อยละ / S.D.)'>";
                                    echo "<i class='fas fa-calculator'></i>";
                                    echo "</a>";
                                    echo "</div>";
                                    echo "</td>";

                                    echo "</tr>";
                                }

                                if ($a == 0) {
                                    echo "<tr class='empty-row'><td colspan='4'>
                                            <div class='empty-state'>
                                                <i class='fas fa-folder-open'></i>
                                                <h6 class='fw-semibold mb-1'>ยังไม่มีโครงการ/กิจกรรม</h6>
                                                <p class='small mb-0'>กรุณาเพิ่มโครงการในระบบก่อนเพื่อพิมพ์แบบประเมิน</p>
                                            </div>
                                          </td></tr>";
                                }
                            } catch (PDOException $e) {
                                error_log("paper_satisfied.php Error: " . $e->getMessage());
                                echo "<tr><td colspan='4' class='text-center text-danger py-4'>
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
                            <i class="fas fa-info-circle me-1" style="color: var(--success);"></i>
                            คลิกที่ไอคอน <i class="fas fa-star" style="color: var(--success);"></i> เพื่อพิมพ์แบบประเมินความพึงพอใจในแท็บใหม่
                        </span>
                        <span class="badge px-3 py-2 rounded-pill" style="background: #d1fae5; color: #047857;">
                            <i class="fas fa-folder me-1"></i>
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
                { targets: [0, 1, 3], className: 'dt-center' },
                { targets: [3], orderable: false }
            ]
        });
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
</body>
</html>