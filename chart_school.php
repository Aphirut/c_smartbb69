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

    <title>กราฟแสดงข้อมูลหน่วยงาน | C-Smart</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --purple: #8b5cf6;
            --pink: #ec4899;
        }

        body{
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf5 100%) !important;
            font-family: 'Kanit', sans-serif;
            min-height: 100vh;
        }

        body::before{
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(circle at 15% 30%, rgba(99, 102, 241, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 85% 70%, rgba(236, 72, 153, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 90%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .app-main{
            position: relative;
            z-index: 1;
        }

        /* === Page Header === */
        .app-content-header{
            padding: 28px 0 18px;
        }

        .page-title{
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 0;
        }

        .page-title-icon{
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            -webkit-text-fill-color: #fff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
        }

        .breadcrumb{
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 8px 18px;
            border-radius: 50px;
            margin: 0;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .breadcrumb-item a{
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color .2s;
        }

        .breadcrumb-item a:hover{
            color: #6366f1;
        }

        .breadcrumb-item.active{
            color: #6366f1;
            font-weight: 600;
        }

        /* === Total Card (เด่น) === */
        .total-card{
            border: none;
            border-radius: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 15px 40px rgba(118, 75, 162, 0.25);
            transition: all .4s ease;
            min-height: 140px;
        }

        .total-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 25px 50px rgba(118, 75, 162, 0.35);
        }

        .total-card::before{
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -60px;
            right: -60px;
            transition: transform .5s ease;
        }

        .total-card::after{
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            bottom: -40px;
            left: -20px;
        }

        .total-card:hover::before{
            transform: scale(1.3) rotate(45deg);
        }

        .total-card .card-body{
            position: relative;
            z-index: 2;
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .total-icon{
            width: 76px;
            height: 76px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transition: transform .3s ease;
            flex-shrink: 0;
        }

        .total-card:hover .total-icon{
            transform: rotate(-8deg) scale(1.1);
        }

        .total-label{
            font-size: 13px;
            font-weight: 500;
            opacity: .95;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .total-number{
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
            margin: 6px 0 4px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .total-sub{
            font-size: 13px;
            opacity: .85;
            font-weight: 300;
        }

        /* === Status Mini Cards === */
        .stat-mini-card{
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all .35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .stat-mini-card::before{
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color);
        }

        .stat-mini-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-mini-card .card-body{
            padding: 20px 16px;
            text-align: center;
        }

        .stat-badge{
            display: inline-block;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 50px;
            color: #fff;
            background: var(--card-color);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-number{
            font-size: 32px;
            font-weight: 800;
            color: var(--card-color);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-percent{
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .stat-progress{
            height: 5px;
            background: #f1f5f9;
            border-radius: 50px;
            overflow: hidden;
        }

        .stat-progress-fill{
            height: 100%;
            background: var(--card-color);
            border-radius: 50px;
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* === Main Chart Card === */
        .main-card{
            border: none;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.6);
            overflow: hidden;
        }

        .main-card-header{
            background: transparent;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            padding: 22px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .main-card-title{
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .main-card-title .icon{
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 6px 14px rgba(99, 102, 241, 0.3);
        }

        .live-badge{
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .live-dot{
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse{
            0%, 100%{ opacity: 1; transform: scale(1); }
            50%{ opacity: 0.6; transform: scale(1.2); }
        }

        /* === Chart Container === */
        .chart-wrapper{
            position: relative;
            max-width: 480px;
            margin: 0 auto;
            padding: 20px;
        }

        .chart-center{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            z-index: 2;
        }

        .chart-center-number{
            font-size: 38px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .chart-center-label{
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === Legend === */
        .modern-legend{
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .legend-card{
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #f1f5f9;
            transition: all .3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .legend-card::before{
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--legend-color);
        }

        .legend-card:hover{
            transform: translateX(5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-color: var(--legend-color);
        }

        .legend-dot{
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--legend-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        .legend-text{
            flex: 1;
            min-width: 0;
        }

        .legend-name{
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .legend-meta{
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .legend-percent{
            font-size: 18px;
            font-weight: 800;
            color: var(--legend-color);
            flex-shrink: 0;
        }

        /* === Empty State === */
        .empty-state{
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i{
            font-size: 72px;
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        /* === Animations === */
        @keyframes fadeInUp{
            from{
                opacity: 0;
                transform: translateY(30px);
            }
            to{
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-up{
            animation: fadeInUp .6s ease-out both;
        }

        .delay-1{ animation-delay: .1s; }
        .delay-2{ animation-delay: .2s; }
        .delay-3{ animation-delay: .3s; }
        .delay-4{ animation-delay: .4s; }

        /* === Responsive === */
        @media (max-width: 768px){
            .page-title{
                font-size: 22px;
            }
            .page-title-icon{
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            .total-number{
                font-size: 32px;
            }
            .chart-center-number{
                font-size: 30px;
            }
            .main-card-header{
                padding: 18px 20px;
            }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <h1 class="page-title">
                        <span class="page-title-icon">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        กราฟแสดงข้อมูลหน่วยงาน
                    </h1>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <nav aria-label="breadcrumb" class="d-inline-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.php">
                                    <i class="fas fa-home me-1"></i>หน้าหลัก
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                กราฟแสดงข้อมูลหน่วยงาน
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid">

            <?php
            $labels = [];
            $data = [];
            $colors = [
                '#6366f1', '#10b981', '#f59e0b', '#ef4444',
                '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6'
            ];
            $statusData = [];

            try {
                $sql = "SELECT status.status_name, COUNT(school.school_id) AS number
                        FROM school 
                        INNER JOIN status ON school.status = status.status_id 
                        GROUP BY status.status_id
                        ORDER BY status.status_id";

                $stmt = $pdo->query($sql);
                $totalSchools = 0;

                while ($row = $stmt->fetch()) {
                    $labels[] = $row['status_name'];
                    $data[] = (int)$row['number'];
                    $totalSchools += (int)$row['number'];
                    $statusData[] = $row;
                }
            } catch (PDOException $e) {
                error_log("chart_school.php Error: " . $e->getMessage());
                echo '<div class="alert alert-danger rounded-4 shadow-sm">เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง</div>';
                $labels = [];
                $data = [];
                $totalSchools = 0;
            }
            ?>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">

                <!-- Total Card -->
                <div class="col-12 col-lg-4 animate-up delay-1">
                    <div class="card total-card h-100">
                        <div class="card-body">
                            <div class="total-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <div class="total-label">
                                    <i class="fas fa-database me-1"></i>
                                    หน่วยงานทั้งหมด
                                </div>
                                <div class="total-number" data-count="<?= $totalSchools ?>">
                                    0
                                </div>
                                <div class="total-sub">
                                    รวมทุกสถานะในระบบ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Mini Cards -->
                <div class="col-12 col-lg-8">
                    <div class="row g-3 h-100">
                        <?php foreach ($statusData as $index => $status): 
                            $color = $colors[$index % count($colors)];
                            $percent = $totalSchools > 0 ? round(($status['number'] / $totalSchools) * 100, 1) : 0;
                            $delay = $index + 2;
                        ?>
                        <div class="col-6 col-md-4 col-lg-3 animate-up delay-<?= min($delay, 4) ?>">
                            <div class="card stat-mini-card h-100" style="--card-color: <?= $color ?>;">
                                <div class="card-body">
                                    <div class="stat-badge">
                                        <?= htmlspecialchars($status['status_name']) ?>
                                    </div>
                                    <div class="stat-number" data-count="<?= $status['number'] ?>">
                                        0
                                    </div>
                                    <div class="stat-percent">
                                        <i class="fas fa-percentage" style="font-size:9px;"></i>
                                        <?= $percent ?>% ของทั้งหมด
                                    </div>
                                    <div class="stat-progress">
                                        <div class="stat-progress-fill" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Main Chart Card -->
            <div class="row animate-up delay-4">
                <div class="col-12">
                    <div class="card main-card">

                        <div class="main-card-header">
                            <h5 class="main-card-title">
                                <span class="icon">
                                    <i class="fas fa-chart-pie"></i>
                                </span>
                                สัดส่วนหน่วยงานตามสถานะ
                            </h5>
                            <?php if (!empty($labels)): ?>
                            <div class="live-badge">
                                <span class="live-dot"></span>
                                ข้อมูลปัจจุบัน
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body p-4">
                            <?php if (!empty($labels)): ?>
                                <div class="row align-items-center g-4">

                                    <!-- Chart -->
                                    <div class="col-lg-7">
                                        <div class="chart-wrapper">
                                            <canvas id="schoolChart" style="max-height: 420px;"></canvas>
                                            <div class="chart-center">
                                                <div class="chart-center-number" data-count="<?= $totalSchools ?>">0</div>
                                                <div class="chart-center-label">หน่วยงานทั้งหมด</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Legend -->
                                    <div class="col-lg-5">
                                        <div class="modern-legend">
                                            <?php foreach ($labels as $i => $label): 
                                                $color = $colors[$i % count($colors)];
                                                $value = $data[$i];
                                                $percent = $totalSchools > 0 ? round(($value / $totalSchools) * 100, 1) : 0;
                                            ?>
                                            <div class="legend-card" style="--legend-color: <?= $color ?>;">
                                                <div class="legend-dot">
                                                    <?= $i + 1 ?>
                                                </div>
                                                <div class="legend-text">
                                                    <div class="legend-name" title="<?= htmlspecialchars($label) ?>">
                                                        <?= htmlspecialchars($label) ?>
                                                    </div>
                                                    <div class="legend-meta">
                                                        <i class="fas fa-school" style="font-size:10px;"></i>
                                                        <?= number_format($value) ?> แห่ง
                                                    </div>
                                                </div>
                                                <div class="legend-percent">
                                                    <?= $percent ?>%
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-chart-pie"></i>
                                    <h4 class="text-muted mb-2">ไม่พบข้อมูลหน่วยงาน</h4>
                                    <p class="text-secondary">ยังไม่มีข้อมูลหน่วยงานในระบบ</p>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Counter Animation -->
<script>
document.addEventListener('DOMContentLoaded', function(){

    const counters = document.querySelectorAll('[data-count]');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 1800;
        const steps = 60;
        const stepValue = target / steps;
        let current = 0;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += stepValue;
            if(step >= steps){
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString('th-TH');
        }, duration / steps);
    });

});
</script>

<!-- Chart -->
<script>
<?php if (!empty($labels)): ?>

const ctx = document.getElementById('schoolChart');

const chartColors = <?= json_encode($colors) ?>;

// สร้าง gradient สำหรับแต่ละสี
function createGradient(ctx, color){
    const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, 200);
    gradient.addColorStop(0, color + 'EE');
    gradient.addColorStop(1, color);
    return gradient;
}

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{
            data: <?= json_encode($data) ?>,
            backgroundColor: chartColors,
            borderWidth: 4,
            borderColor: '#ffffff',
            hoverBorderWidth: 6,
            hoverOffset: 20,
            hoverBorderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '68%',
        radius: '92%',
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 1800,
            easing: 'easeOutCubic'
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                padding: 16,
                cornerRadius: 12,
                displayColors: true,
                boxPadding: 6,
                titleFont: {
                    size: 14,
                    weight: 'bold',
                    family: 'Kanit'
                },
                bodyFont: {
                    size: 13,
                    family: 'Kanit'
                },
                titleColor: '#fbbf24',
                bodyColor: '#fff',
                borderColor: 'rgba(99, 102, 241, 0.4)',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.raw / total) * 100).toFixed(1);
                        return [
                            '  ' + context.label,
                            '  📊 จำนวน: ' + context.raw.toLocaleString() + ' แห่ง',
                            '  📈 สัดส่วน: ' + percentage + '%'
                        ];
                    },
                    title: function() {
                        return '';
                    }
                }
            }
        }
    }
});

<?php endif; ?>
</script>

</body>
</html>