<!DOCTYPE html>
<html lang="th">

<head>
    <?php
    session_name("C_Smart");
    session_start();
    include "./modules/head.inc.php";
    include "./includes/connect.php";
    ?>

    <title>กราฟแสดงข้อมูลผู้เรียนชาย/หญิง | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Sarabun", "TH Sarabun New", sans-serif;
            background: #f4f7fb;
        }

        .page-hero {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            padding: 30px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 55%, #ec4899 100%);
            box-shadow: 0 18px 45px rgba(37, 99, 235, 0.25);
        }

        .page-hero::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            right: -90px;
            top: -130px;
            background: rgba(255, 255, 255, 0.16);
        }

        .page-hero::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            left: 140px;
            bottom: -95px;
            background: rgba(255, 255, 255, 0.13);
        }

        .page-hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            backdrop-filter: blur(8px);
        }

        .modern-card {
            border: 0;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 46px rgba(15, 23, 42, 0.13);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex: 0 0 auto;
        }

        .stat-title {
            color: #64748b;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .stat-number {
            font-size: 2.1rem;
            line-height: 1.15;
            font-weight: 800;
        }

        .stat-subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
        }

        .gender-male {
            color: #2563eb;
        }

        .gender-female {
            color: #ec4899;
        }

        .chart-card {
            border: 0;
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 14px 42px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .chart-card-header {
            padding: 24px 28px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 430px;
            max-width: 520px;
            margin: 0 auto;
        }

        .ratio-panel {
            border-radius: 24px;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            padding: 22px;
        }

        .ratio-item {
            padding: 18px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #edf2f7;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            margin-bottom: 16px;
        }

        .ratio-item:last-child {
            margin-bottom: 0;
        }

        .ratio-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .ratio-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        .ratio-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
        }

        .ratio-value {
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
        }

        .progress {
            height: 10px;
            border-radius: 999px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 999px;
        }

        .summary-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 14px;
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-size: 0.92rem;
            backdrop-filter: blur(8px);
        }

        .empty-state {
            padding: 70px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 18px;
        }

        .breadcrumb a {
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .page-hero {
                padding: 24px;
            }

            .chart-wrapper {
                height: 360px;
            }
        }

        @media (max-width: 575px) {
            .page-hero h3 {
                font-size: 1.35rem;
            }

            .chart-wrapper {
                height: 310px;
            }

            .stat-number {
                font-size: 1.75rem;
            }

            .stat-icon {
                width: 56px;
                height: 56px;
                font-size: 26px;
            }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <?php
    $menCount = 0;
    $womenCount = 0;
    $totalStudents = 0;
    $malePercent = 0;
    $femalePercent = 0;

    try {
        $sql = "
            SELECT 
                COALESCE(SUM(men_study), 0) AS men_count, 
                COALESCE(SUM(women_study), 0) AS women_count 
            FROM activity
        ";

        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $menCount = (int)($row['men_count'] ?? 0);
        $womenCount = (int)($row['women_count'] ?? 0);
        $totalStudents = $menCount + $womenCount;

        if ($totalStudents > 0) {
            $malePercent = round(($menCount / $totalStudents) * 100, 1);
            $femalePercent = round(($womenCount / $totalStudents) * 100, 1);
        }

    } catch (PDOException $e) {
        error_log("chart_sex.php Error: " . $e->getMessage());
        echo '
        <div class="alert alert-danger m-3 rounded-4 shadow-sm">
            <strong>เกิดข้อผิดพลาด:</strong> ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง
        </div>';
    }
    ?>

    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">

            <div class="page-hero mb-4">
                <div class="page-hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="hero-icon">
                                    <i class="fas fa-users"></i>
                                </div>

                                <div>
                                    <h3 class="mb-1 fw-bold">
                                        กราฟแสดงข้อมูลผู้เรียนชาย / หญิง
                                    </h3>
                                    <div class="opacity-75">
                                        สรุปจำนวนผู้เรียนทั้งหมด พร้อมแสดงสัดส่วนชายและหญิงด้วย Chart.js
                                    </div>

                                    <div class="mt-3">
                                        <span class="summary-pill">
                                            <i class="fas fa-user-graduate"></i>
                                            รวม <?= number_format($totalStudents) ?> คน
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-lg-end mb-0">
                                    <li class="breadcrumb-item">
                                        <a class="text-white opacity-75" href="index.php">หน้าหลัก</a>
                                    </li>
                                    <li class="breadcrumb-item active text-white" aria-current="page">
                                        ผู้เรียนชาย/หญิง
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">

                <div class="col-12 col-md-4">
                    <div class="card modern-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="fas fa-users"></i>
                                </div>

                                <div>
                                    <div class="stat-title">ผู้เรียนทั้งหมด</div>
                                    <div class="stat-number text-primary">
                                        <?= number_format($totalStudents) ?>
                                    </div>
                                    <div class="stat-subtitle">คน</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <div class="card modern-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="fas fa-male"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="stat-title">ผู้เรียนชาย</div>
                                    <div class="stat-number gender-male">
                                        <?= number_format($menCount) ?>
                                    </div>
                                    <div class="stat-subtitle">
                                        <?= $malePercent ?>% ของทั้งหมด
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <div class="card modern-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                                    <i class="fas fa-female"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="stat-title">ผู้เรียนหญิง</div>
                                    <div class="stat-number gender-female">
                                        <?= number_format($womenCount) ?>
                                    </div>
                                    <div class="stat-subtitle">
                                        <?= $femalePercent ?>% ของทั้งหมด
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-card">

                        <div class="chart-card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-1 fw-bold d-flex align-items-center">
                                        <i class="fas fa-chart-pie text-primary me-2"></i>
                                        สัดส่วนผู้เรียนชาย / หญิง
                                    </h5>
                                    <div class="text-muted small">
                                        แสดงจำนวนและเปอร์เซ็นต์ของผู้เรียนแยกตามเพศ
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                                        รวม <?= number_format($totalStudents) ?> คน
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">

                            <?php if ($totalStudents > 0): ?>

                                <div class="row align-items-center g-4">

                                    <!-- Chart -->
                                    <div class="col-lg-7">
                                        <div class="chart-wrapper">
                                            <canvas id="sexChart"></canvas>
                                        </div>
                                    </div>

                                    <!-- Ratio Panel -->
                                    <div class="col-lg-5">
                                        <div class="ratio-panel">

                                            <div class="ratio-item">
                                                <div class="ratio-top">
                                                    <div class="ratio-label">
                                                        <span class="ratio-dot" style="background:#2563eb;"></span>
                                                        <span>ผู้เรียนชาย</span>
                                                    </div>

                                                    <div class="ratio-value">
                                                        <?= number_format($menCount) ?> คน
                                                    </div>
                                                </div>

                                                <div class="progress">
                                                    <div class="progress-bar"
                                                         style="width: <?= $malePercent ?>%; background:#2563eb;"
                                                         role="progressbar"
                                                         aria-valuenow="<?= $malePercent ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="small text-muted mt-2">
                                                    คิดเป็น <?= $malePercent ?>% ของผู้เรียนทั้งหมด
                                                </div>
                                            </div>

                                            <div class="ratio-item">
                                                <div class="ratio-top">
                                                    <div class="ratio-label">
                                                        <span class="ratio-dot" style="background:#ec4899;"></span>
                                                        <span>ผู้เรียนหญิง</span>
                                                    </div>

                                                    <div class="ratio-value">
                                                        <?= number_format($womenCount) ?> คน
                                                    </div>
                                                </div>

                                                <div class="progress">
                                                    <div class="progress-bar"
                                                         style="width: <?= $femalePercent ?>%; background:#ec4899;"
                                                         role="progressbar"
                                                         aria-valuenow="<?= $femalePercent ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="small text-muted mt-2">
                                                    คิดเป็น <?= $femalePercent ?>% ของผู้เรียนทั้งหมด
                                                </div>
                                            </div>

                                            <div class="mt-4 p-3 rounded-4 bg-light border">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3 text-primary fs-4">
                                                        <i class="fas fa-circle-info"></i>
                                                    </div>

                                                    <div>
                                                        <div class="fw-bold text-dark">
                                                            ข้อมูลรวมจากตารางกิจกรรม
                                                        </div>
                                                        <div class="small text-muted">
                                                            คำนวณจากผลรวมช่อง men_study และ women_study ในตาราง activity
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            <?php else: ?>

                                <div class="empty-state">
                                    <i class="fas fa-chart-pie"></i>
                                    <h5 class="fw-bold mb-2">ไม่พบข้อมูลผู้เรียน</h5>
                                    <div>ยังไม่มีข้อมูลจำนวนผู้เรียนชาย/หญิงสำหรับนำมาแสดงเป็นกราฟ</div>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<?php include "./modules/footer.inc.php"; ?>

</div><!-- /.app-wrapper -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
<?php if ($totalStudents > 0): ?>

document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById("sexChart");

    const menCount = <?= (int)$menCount ?>;
    const womenCount = <?= (int)$womenCount ?>;
    const totalStudents = <?= (int)$totalStudents ?>;

    const centerTextPlugin = {
        id: "centerTextPlugin",
        afterDraw(chart) {
            const { ctx, chartArea } = chart;

            if (!chartArea) {
                return;
            }

            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;

            ctx.save();
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";

            ctx.font = '800 32px "Sarabun", sans-serif';
            ctx.fillStyle = "#0f172a";
            ctx.fillText(totalStudents.toLocaleString("th-TH"), centerX, centerY - 12);

            ctx.font = '500 15px "Sarabun", sans-serif';
            ctx.fillStyle = "#64748b";
            ctx.fillText("ผู้เรียนทั้งหมด", centerX, centerY + 22);

            ctx.restore();
        }
    };

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["ผู้เรียนชาย", "ผู้เรียนหญิง"],
            datasets: [{
                data: [menCount, womenCount],
                backgroundColor: [
                    "#2563eb",
                    "#ec4899"
                ],
                borderColor: "#ffffff",
                borderWidth: 5,
                hoverBorderWidth: 6,
                hoverOffset: 14,
                borderRadius: 10,
                spacing: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "64%",
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 900,
                easing: "easeOutQuart"
            },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true,
                        pointStyle: "circle",
                        padding: 22,
                        color: "#0f172a",
                        font: {
                            family: "Sarabun",
                            size: 14,
                            weight: "600"
                        }
                    }
                },
                tooltip: {
                    backgroundColor: "rgba(15, 23, 42, 0.95)",
                    titleColor: "#ffffff",
                    bodyColor: "#ffffff",
                    borderColor: "rgba(255,255,255,0.18)",
                    borderWidth: 1,
                    padding: 14,
                    cornerRadius: 14,
                    displayColors: true,
                    titleFont: {
                        size: 14,
                        weight: "bold",
                        family: "Sarabun"
                    },
                    bodyFont: {
                        size: 13,
                        family: "Sarabun"
                    },
                    callbacks: {
                        label: function (context) {
                            const value = Number(context.raw);
                            const percent = totalStudents > 0
                                ? ((value / totalStudents) * 100).toFixed(1)
                                : 0;

                            return " " +
                                context.label + ": " +
                                value.toLocaleString("th-TH") +
                                " คน (" + percent + "%)";
                        }
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });

});

<?php endif; ?>
</script>

</body>
</html>