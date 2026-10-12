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

    <title>กราฟแสดงข้อมูลค่าใช้จ่ายโครงการ | C-Smart</title>

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
            background: linear-gradient(135deg, #0f766e 0%, #2563eb 55%, #7c3aed 100%);
            border-radius: 26px;
            padding: 30px;
            color: #ffffff;
            box-shadow: 0 20px 50px rgba(37, 99, 235, 0.24);
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            top: -130px;
            right: -90px;
        }

        .page-hero::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
            bottom: -85px;
            left: 110px;
        }

        .page-hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.18);
        }

        .hero-subtitle {
            color: rgba(255,255,255,0.82);
            font-size: 0.98rem;
        }

        .modern-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
            transition: all 0.25s ease;
            background: #ffffff;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 48px rgba(15, 23, 42, 0.13);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex: 0 0 auto;
        }

        .stat-title {
            color: #64748b;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .chart-card {
            border: 0;
            border-radius: 28px;
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
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
            height: 440px;
            min-height: 360px;
        }

        .expense-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .expense-item {
            display: flex;
            gap: 14px;
            padding: 15px 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            transition: all 0.2s ease;
        }

        .expense-item:hover {
            background: #eef6ff;
            transform: translateX(3px);
        }

        .expense-color {
            width: 18px;
            min-width: 18px;
            height: 46px;
            border-radius: 999px;
        }

        .expense-label {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .expense-value {
            font-size: 0.9rem;
            color: #475569;
        }

        .expense-money {
            font-weight: 800;
            color: #0f172a;
        }

        .soft-progress {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .soft-progress-bar {
            height: 100%;
            border-radius: 999px;
        }

        .badge-soft {
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 700;
            background: #eff6ff;
            color: #2563eb;
        }

        .empty-state {
            padding: 75px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-state i {
            font-size: 66px;
            color: #cbd5e1;
            margin-bottom: 18px;
        }

        .breadcrumb a {
            text-decoration: none;
        }

        .summary-note {
            background: linear-gradient(135deg, #f8fafc 0%, #eef6ff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            padding: 18px 20px;
            color: #475569;
        }

        @media (max-width: 991px) {
            .chart-wrapper {
                height: 380px;
            }

            .page-hero {
                padding: 24px;
            }

            .stat-number {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 575px) {
            .chart-wrapper {
                height: 330px;
            }

            .page-hero h3 {
                font-size: 1.35rem;
            }

            .hero-icon {
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
    $costLabels = [];
    $costData = [];
    $costDetails = [];

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

    $totalActivities = 0;
    $grandTotal = 0;
    $averageCost = 0;
    $topCostLabel = '-';
    $topCostValue = 0;

    try {
        $sql = "
            SELECT 
                COUNT(activity_id) AS total,
                COALESCE(SUM(lecturer_cost), 0) AS lecturer_cost,
                COALESCE(SUM(material_cost), 0) AS material_cost,
                COALESCE(SUM(board_cost), 0) AS board_cost,
                COALESCE(SUM(food_cost), 0) AS food_cost,
                COALESCE(SUM(drink_cost), 0) AS drink_cost,
                COALESCE(SUM(place_cost), 0) AS place_cost,
                COALESCE(SUM(car_cost), 0) AS car_cost,
                COALESCE(SUM(other_cost), 0) AS other_cost,
                COALESCE(SUM(total_costs), 0) AS total_costs
            FROM activity
        ";

        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalActivities = (int)($row['total'] ?? 0);
        $grandTotal = (float)($row['total_costs'] ?? 0);
        $averageCost = $totalActivities > 0 ? ($grandTotal / $totalActivities) : 0;

        $costItems = [
            ['label' => 'ค่าวิทยากร', 'value' => (float)($row['lecturer_cost'] ?? 0), 'icon' => 'fa-chalkboard-user'],
            ['label' => 'ค่าวัสดุ', 'value' => (float)($row['material_cost'] ?? 0), 'icon' => 'fa-boxes-stacked'],
            ['label' => 'ค่าป้าย', 'value' => (float)($row['board_cost'] ?? 0), 'icon' => 'fa-sign-hanging'],
            ['label' => 'ค่าอาหาร', 'value' => (float)($row['food_cost'] ?? 0), 'icon' => 'fa-utensils'],
            ['label' => 'ค่าอาหารว่าง', 'value' => (float)($row['drink_cost'] ?? 0), 'icon' => 'fa-mug-hot'],
            ['label' => 'ค่าเช่าสถานที่', 'value' => (float)($row['place_cost'] ?? 0), 'icon' => 'fa-building'],
            ['label' => 'ค่าจ้างเหมารถ', 'value' => (float)($row['car_cost'] ?? 0), 'icon' => 'fa-van-shuttle'],
            ['label' => 'ค่าใช้จ่ายอื่น', 'value' => (float)($row['other_cost'] ?? 0), 'icon' => 'fa-receipt'],
        ];

        foreach ($costItems as $item) {
            if ($item['value'] > 0) {
                $costLabels[] = $item['label'];
                $costData[] = $item['value'];
                $costDetails[] = $item;
            }
        }

        if (!empty($costDetails)) {
            usort($costDetails, function ($a, $b) {
                return $b['value'] <=> $a['value'];
            });

            $topCostLabel = $costDetails[0]['label'];
            $topCostValue = $costDetails[0]['value'];

            $costLabels = [];
            $costData = [];

            foreach ($costDetails as $item) {
                $costLabels[] = $item['label'];
                $costData[] = $item['value'];
            }
        }

    } catch (PDOException $e) {
        error_log("chart_activity_money.php Error: " . $e->getMessage());
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
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold">
                                        กราฟแสดงข้อมูลค่าใช้จ่ายโครงการ
                                    </h3>
                                    <div class="hero-subtitle">
                                        ภาพรวมงบประมาณและค่าใช้จ่ายแยกตามประเภท ด้วยกราฟ Chart.js
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
                                        ค่าใช้จ่ายโครงการ
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
                                    <div class="stat-title">จำนวนโครงการทั้งหมด</div>
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
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <div class="stat-title">ค่าใช้จ่ายรวมทั้งหมด</div>
                                    <div class="stat-number text-success">
                                        <?= number_format($grandTotal, 0) ?>
                                    </div>
                                    <div class="text-muted small">บาท</div>
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
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div>
                                    <div class="stat-title">ค่าใช้จ่ายเฉลี่ยต่อโครงการ</div>
                                    <div class="stat-number text-info">
                                        <?= number_format($averageCost, 0) ?>
                                    </div>
                                    <div class="text-muted small">บาท / กิจกรรม</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Extra Summary -->
            <div class="summary-note mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary fs-3">
                                <i class="fas fa-circle-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    ประเภทค่าใช้จ่ายที่มีมูลค่าสูงที่สุด
                                </div>
                                <div class="small">
                                    <?= htmlspecialchars($topCostLabel, ENT_QUOTES, 'UTF-8') ?>
                                    จำนวน
                                    <strong><?= number_format($topCostValue, 0) ?></strong>
                                    บาท
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                        <span class="badge-soft">
                            <i class="fas fa-layer-group me-1"></i>
                            <?= number_format(count($costLabels)) ?> ประเภทค่าใช้จ่าย
                        </span>
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
                                        <i class="fas fa-chart-bar text-primary me-2"></i>
                                        ค่าใช้จ่ายแยกตามประเภท
                                    </h5>
                                    <div class="text-muted small">
                                        แสดงมูลค่าค่าใช้จ่ายแต่ละประเภท พร้อมเปอร์เซ็นต์เทียบกับค่าใช้จ่ายรวม
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                                        รวม <?= number_format($grandTotal, 0) ?> บาท
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">

                            <?php if (!empty($costLabels)): ?>

                                <div class="row align-items-center g-4">

                                    <!-- Chart -->
                                    <div class="col-lg-8">
                                        <div class="chart-wrapper">
                                            <canvas id="moneyChart"></canvas>
                                        </div>
                                    </div>

                                    <!-- Detail List -->
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <div class="fw-bold text-dark">
                                                รายละเอียดค่าใช้จ่าย
                                            </div>
                                            <div class="small text-muted">
                                                เรียงจากมูลค่าสูงไปต่ำ
                                            </div>
                                        </div>

                                        <div class="expense-list">
                                            <?php foreach ($costDetails as $i => $item): ?>
                                                <?php
                                                $color = $colors[$i % count($colors)];
                                                $percent = $grandTotal > 0 ? round(($item['value'] / $grandTotal) * 100, 1) : 0;
                                                ?>

                                                <div class="expense-item">
                                                    <div class="expense-color"
                                                         style="background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;">
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                                            <div>
                                                                <div class="expense-label">
                                                                    <i class="fas <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?> me-1 text-muted"></i>
                                                                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                                                                </div>
                                                                <div class="expense-value">
                                                                    <span class="expense-money">
                                                                        <?= number_format($item['value'], 0) ?>
                                                                    </span>
                                                                    บาท
                                                                    <span class="text-muted">
                                                                        (<?= $percent ?>%)
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="soft-progress mt-2">
                                                            <div class="soft-progress-bar"
                                                                 style="width: <?= $percent ?>%; background: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;">
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
                                    <i class="fas fa-chart-bar"></i>
                                    <h5 class="fw-bold mb-2">ไม่พบข้อมูลค่าใช้จ่าย</h5>
                                    <div>ยังไม่มีข้อมูลค่าใช้จ่ายสำหรับนำมาแสดงเป็นกราฟ</div>
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
<?php if (!empty($costLabels)): ?>

document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById("moneyChart");

    const chartLabels = <?= json_encode($costLabels, JSON_UNESCAPED_UNICODE) ?>;
    const chartData = <?= json_encode($costData) ?>;
    const chartColors = <?= json_encode($colors) ?>;
    const grandTotal = <?= (float)$grandTotal ?>;

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartLabels,
            datasets: [{
                label: "ค่าใช้จ่าย",
                data: chartData,
                backgroundColor: chartColors,
                borderColor: "#ffffff",
                borderWidth: 2,
                borderRadius: 12,
                borderSkipped: false,
                barThickness: 30,
                maxBarThickness: 38
            }]
        },
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            animation: {
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
                    borderColor: "rgba(255,255,255,0.16)",
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
                            const percent = grandTotal > 0
                                ? ((value / grandTotal) * 100).toFixed(1)
                                : 0;

                            return " " +
                                value.toLocaleString("th-TH") +
                                " บาท (" + percent + "%)";
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: "#eef2f7",
                        drawBorder: false
                    },
                    ticks: {
                        color: "#64748b",
                        font: {
                            family: "Sarabun",
                            size: 12
                        },
                        callback: function (value) {
                            return Number(value).toLocaleString("th-TH");
                        }
                    },
                    title: {
                        display: true,
                        text: "จำนวนเงิน (บาท)",
                        color: "#64748b",
                        font: {
                            family: "Sarabun",
                            size: 13,
                            weight: "600"
                        }
                    }
                },
                y: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: "#0f172a",
                        font: {
                            family: "Sarabun",
                            size: 13,
                            weight: "600"
                        }
                    }
                }
            }
        }
    });

});

<?php endif; ?>
</script>

</body>
</html>