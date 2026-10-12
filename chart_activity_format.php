<?php
session_name("C_Smart");
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php
    include "./modules/head.inc.php";
    include "./includes/connect.php";
    ?>

    <title>กราฟแสดงข้อมูลรูปแบบโครงการ | C-Smart</title>

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
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            border-radius: 24px;
            padding: 28px;
            color: #ffffff;
            box-shadow: 0 18px 45px rgba(37, 99, 235, 0.25);
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            top: -120px;
            right: -80px;
        }

        .page-hero::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            bottom: -80px;
            left: 120px;
        }

        .page-hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(8px);
        }

        .modern-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            transition: all 0.25s ease;
            overflow: hidden;
            background: #ffffff;
        }

        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .stat-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .stat-title {
            color: #64748b;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .stat-number {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .chart-card {
            border: 0;
            border-radius: 26px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
            background: #ffffff;
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

        .legend-box {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .legend-item-modern {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            transition: all 0.2s ease;
        }

        .legend-item-modern:hover {
            background: #eef6ff;
            transform: translateX(3px);
        }

        .legend-color-modern {
            width: 18px;
            height: 42px;
            border-radius: 999px;
            flex: 0 0 auto;
        }

        .legend-label {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .legend-detail {
            font-size: 0.88rem;
            color: #64748b;
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
            .chart-wrapper {
                height: 360px;
            }

            .page-hero {
                padding: 22px;
            }

            .stat-number {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 575px) {
            .chart-wrapper {
                height: 310px;
            }

            .page-hero h3 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <?php
    $labels = [];
    $data = [];
    $colors = [
        '#2563eb',
        '#16a34a',
        '#f59e0b',
        '#ef4444',
        '#7c3aed',
        '#06b6d4',
        '#f97316',
        '#ec4899',
        '#14b8a6',
        '#64748b'
    ];

    $formatData = [];
    $totalActivities = 0;

    try {
        $sql = "
            SELECT 
                format.format_name, 
                COUNT(activity.format_id) AS number
            FROM activity
            INNER JOIN format 
                ON activity.format_id = format.format_id
            GROUP BY 
                format.format_id, 
                format.format_name
            ORDER BY number DESC
        ";

        $stmt = $pdo->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $labels[] = $row['format_name'];
            $data[] = (int)$row['number'];
            $totalActivities += (int)$row['number'];
            $formatData[] = $row;
        }

    } catch (PDOException $e) {
        error_log("chart_activity_format.php Error: " . $e->getMessage());
        echo '
        <div class="alert alert-danger m-3 rounded-4 shadow-sm">
            <strong>เกิดข้อผิดพลาด:</strong> ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง
        </div>';
    }

    $topFormatName = !empty($labels) ? $labels[0] : '-';
    $topFormatCount = !empty($data) ? $data[0] : 0;
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
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold">
                                        กราฟแสดงข้อมูลรูปแบบโครงการ
                                    </h3>
                                    <div class="opacity-75">
                                        ภาพรวมจำนวนกิจกรรม แยกตามรูปแบบโครงการ ด้วยกราฟ Chart.js
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
                                        รูปแบบโครงการ
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
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div>
                                    <div class="stat-title">โครงการทั้งหมด</div>
                                    <div class="stat-number text-primary">
                                        <?= number_format($totalActivities) ?>
                                    </div>
                                    <div class="text-muted small">กิจกรรม</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card modern-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div>
                                    <div class="stat-title">จำนวนรูปแบบโครงการ</div>
                                    <div class="stat-number text-success">
                                        <?= number_format(count($labels)) ?>
                                    </div>
                                    <div class="text-muted small">รูปแบบ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card modern-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                    <i class="fas fa-ranking-star"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="stat-title">รูปแบบที่นิยมที่สุด</div>
                                    <div class="fs-5 fw-bold text-info text-truncate" style="max-width: 220px;"
                                         title="<?= htmlspecialchars($topFormatName, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($topFormatName, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= number_format($topFormatCount) ?> กิจกรรม
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Chart Section -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-card">

                        <div class="chart-card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-1 fw-bold d-flex align-items-center">
                                        <i class="fas fa-chart-pie text-primary me-2"></i>
                                        สัดส่วนรูปแบบโครงการ
                                    </h5>
                                    <div class="text-muted small">
                                        แสดงจำนวนและเปอร์เซ็นต์ของกิจกรรมในแต่ละรูปแบบ
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                                        รวม <?= number_format($totalActivities) ?> กิจกรรม
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">

                            <?php if (!empty($labels)): ?>

                                <div class="row align-items-center g-4">

                                    <!-- Chart -->
                                    <div class="col-lg-7">
                                        <div class="chart-wrapper">
                                            <canvas id="formatChart"></canvas>
                                        </div>
                                    </div>

                                    <!-- Legend -->
                                    <div class="col-lg-5">
                                        <div class="legend-box">

                                            <?php foreach ($labels as $i => $label): ?>
                                                <?php
                                                $color = $colors[$i % count($colors)];
                                                $value = $data[$i];
                                                $percent = $totalActivities > 0
                                                    ? round(($value / $totalActivities) * 100, 1)
                                                    : 0;
                                                ?>

                                                <div class="legend-item-modern">
                                                    <div class="legend-color-modern"
                                                         style="background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;">
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="legend-label">
                                                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                                        </div>

                                                        <div class="legend-detail">
                                                            <?= number_format($value) ?> กิจกรรม
                                                            <span class="mx-1">•</span>
                                                            <?= $percent ?>%
                                                        </div>

                                                        <div class="progress mt-2" style="height: 7px; border-radius: 999px;">
                                                            <div class="progress-bar"
                                                                 role="progressbar"
                                                                 style="width: <?= $percent ?>%; background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;"
                                                                 aria-valuenow="<?= $percent ?>"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php endforeach; ?>

                                        </div>
                                    </div>

                                </div>

                            <?php else: ?>

                                <div class="empty-state">
                                    <i class="fas fa-chart-pie"></i>
                                    <h5 class="fw-bold mb-2">ไม่พบข้อมูลรูปแบบโครงการ</h5>
                                    <div>ยังไม่มีข้อมูลกิจกรรมสำหรับนำมาแสดงเป็นกราฟ</div>
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
<?php if (!empty($labels)): ?>

document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById("formatChart");

    const chartLabels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
    const chartData = <?= json_encode($data) ?>;
    const chartColors = <?= json_encode($colors) ?>;
    const totalActivities = <?= (int)$totalActivities ?>;

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
            ctx.fillText(totalActivities.toLocaleString("th-TH"), centerX, centerY - 12);

            ctx.font = '500 15px "Sarabun", sans-serif';
            ctx.fillStyle = "#64748b";
            ctx.fillText("กิจกรรมทั้งหมด", centerX, centerY + 22);

            ctx.restore();
        }
    };

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: chartColors,
                borderColor: "#ffffff",
                borderWidth: 4,
                hoverBorderWidth: 5,
                hoverOffset: 12,
                borderRadius: 8,
                spacing: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "62%",
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 900,
                easing: "easeOutQuart"
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgba(15, 23, 42, 0.95)",
                    titleColor: "#ffffff",
                    bodyColor: "#ffffff",
                    borderColor: "rgba(255,255,255,0.15)",
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
                            const total = context.dataset.data.reduce(function (a, b) {
                                return Number(a) + Number(b);
                            }, 0);

                            const value = Number(context.raw);
                            const percentage = total > 0
                                ? ((value / total) * 100).toFixed(1)
                                : 0;

                            return " " + context.label + ": " +
                                value.toLocaleString("th-TH") +
                                " กิจกรรม (" + percentage + "%)";
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