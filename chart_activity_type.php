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

    <title>กราฟแสดงข้อมูลประเภทโครงการ | C-Smart</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root{
            --primary: #6366f1;
            --primary-dark: #4f46e5;
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
        }

        .breadcrumb-item a:hover{
            color: #6366f1;
        }

        .breadcrumb-item.active{
            color: #6366f1;
            font-weight: 600;
        }

        /* === Total Card === */
        .total-card{
            border: none;
            border-radius: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 15px 40px rgba(118, 75, 162, 0.25);
            transition: all .4s ease;
            min-height: 160px;
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
            height: 100%;
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
            flex-shrink: 0;
        }

        .total-label{
            font-size: 13px;
            font-weight: 500;
            opacity: .95;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .total-number{
            font-size: 44px;
            font-weight: 800;
            line-height: 1;
            margin: 8px 0 4px;
        }

        .total-sub{
            font-size: 13px;
            opacity: .85;
            font-weight: 300;
        }

        /* === Type Pills === */
        .types-card{
            border: none;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .types-card .card-body{
            padding: 24px;
        }

        .types-label{
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }

        .type-pill{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px 8px 8px;
            background: #fff;
            border-radius: 50px;
            border: 1px solid #f1f5f9;
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            transition: all .25s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .type-pill:hover{
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
            border-color: var(--pill-color);
        }

        .type-pill-dot{
            width: 28px;
            height: 28px;
            background: var(--pill-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            flex-shrink: 0;
        }

        .type-pill-count{
            background: var(--pill-color);
            color: #fff;
            padding: 2px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 12px;
            margin-left: 4px;
        }

        /* === Main Card === */
        .main-card{
            border: none;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
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

        /* === Chart === */
        .chart-wrapper{
            position: relative;
            width: 100%;
            max-width: 460px;
            height: 420px;
            margin: 0 auto;
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
        .legend-title{
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-title i{
            color: #f59e0b;
        }

        .legend-list{
            max-height: 420px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .legend-list::-webkit-scrollbar{
            width: 6px;
        }

        .legend-list::-webkit-scrollbar-track{
            background: #f1f5f9;
            border-radius: 20px;
        }

        .legend-list::-webkit-scrollbar-thumb{
            background: linear-gradient(180deg, #6366f1, #4f46e5);
            border-radius: 20px;
        }

        .legend-card{
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #f1f5f9;
            transition: all .3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
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

        .legend-card.top-rank{
            background: linear-gradient(135deg, #fff 0%, #fefce8 100%);
            border-color: #fde047;
        }

        .legend-dot{
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--legend-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        .legend-medal{
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .medal-1{ background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .medal-2{ background: linear-gradient(135deg, #cbd5e1, #94a3b8); }
        .medal-3{ background: linear-gradient(135deg, #fb923c, #ea580c); }

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

        .empty-state{
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i{
            font-size: 72px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        @media (max-width: 768px){
            .page-title{ font-size: 22px; }
            .page-title-icon{ width: 44px; height: 44px; font-size: 18px; }
            .total-number{ font-size: 34px; }
            .chart-center-number{ font-size: 30px; }
            .chart-wrapper{ height: 360px; }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <h1 class="page-title">
                        <span class="page-title-icon">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        กราฟแสดงข้อมูลประเภทโครงการ
                    </h1>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <nav aria-label="breadcrumb" class="d-inline-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a>
                            </li>
                            <li class="breadcrumb-item active">ประเภทโครงการ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <?php
            $labels = [];
            $data = [];
            $colors = [
                '#6366f1', '#10b981', '#f59e0b', '#ef4444',
                '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6',
                '#3b82f6', '#f97316'
            ];

            $totalProjects = 0;
            $typeData = [];

            try {
                $sql = "SELECT type.type_name, type.type_name_short, COUNT(activity.type_id) AS number
                        FROM activity 
                        INNER JOIN type ON activity.type_id = type.type_id 
                        GROUP BY type.type_id
                        ORDER BY number DESC";

                $stmt = $pdo->query($sql);

                while ($row = $stmt->fetch()) {
                    $labels[] = $row['type_name_short'] ?: $row['type_name'];
                    $data[] = (int)$row['number'];
                    $totalProjects += (int)$row['number'];
                    $typeData[] = $row;
                }
            } catch (PDOException $e) {
                error_log("chart_activity_type.php Error: " . $e->getMessage());
                echo '<div class="alert alert-danger rounded-4 shadow-sm">เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง</div>';
            }
            ?>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">

                <div class="col-12 col-lg-4">
                    <div class="card total-card h-100">
                        <div class="card-body">
                            <div class="total-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div>
                                <div class="total-label">
                                    <i class="fas fa-database me-1"></i>
                                    โครงการทั้งหมด
                                </div>
                                <div class="total-number" data-count="<?= $totalProjects ?>">0</div>
                                <div class="total-sub">
                                    <i class="fas fa-tags me-1"></i>
                                    แบ่งเป็น <?= count($typeData) ?> ประเภท
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="card types-card h-100">
                        <div class="card-body">
                            <div class="types-label">
                                <i class="fas fa-tags text-primary"></i>
                                ประเภทโครงการที่พบในระบบ
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($typeData as $index => $type): 
                                    $color = $colors[$index % count($colors)];
                                    $shortName = $type['type_name_short'] ?: $type['type_name'];
                                ?>
                                    <div class="type-pill" style="--pill-color: <?= $color ?>;" 
                                         title="<?= htmlspecialchars($type['type_name']) ?>">
                                        <span class="type-pill-dot"><?= $index + 1 ?></span>
                                        <?= htmlspecialchars($shortName) ?>
                                        <span class="type-pill-count"><?= number_format($type['number']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="card main-card">

                        <div class="main-card-header">
                            <h5 class="main-card-title">
                                <span class="icon"><i class="fas fa-chart-pie"></i></span>
                                สัดส่วนประเภทโครงการ
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

                                    <div class="col-lg-7">
                                        <div class="chart-wrapper">
                                            <canvas id="typeChart"></canvas>
                                            <div class="chart-center">
                                                <div class="chart-center-number" data-count="<?= $totalProjects ?>">0</div>
                                                <div class="chart-center-label">โครงการทั้งหมด</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-5">
                                        <div class="legend-title">
                                            <i class="fas fa-trophy"></i>
                                            อันดับประเภทโครงการ
                                        </div>
                                        <div class="legend-list">
                                            <?php foreach ($labels as $i => $label): 
                                                $value = $data[$i];
                                                $color = $colors[$i % count($colors)];
                                                $percent = $totalProjects > 0 ? round(($value / $totalProjects) * 100, 1) : 0;
                                                $rank = $i + 1;
                                                $isTop = $rank <= 3;
                                            ?>
                                                <div class="legend-card <?= $isTop ? 'top-rank' : '' ?>" 
                                                     style="--legend-color: <?= $color ?>;">

                                                    <?php if($rank == 1): ?>
                                                        <div class="legend-medal medal-1"><i class="fas fa-crown"></i></div>
                                                    <?php elseif($rank == 2): ?>
                                                        <div class="legend-medal medal-2"><i class="fas fa-medal"></i></div>
                                                    <?php elseif($rank == 3): ?>
                                                        <div class="legend-medal medal-3"><i class="fas fa-award"></i></div>
                                                    <?php else: ?>
                                                        <div class="legend-dot"><?= $rank ?></div>
                                                    <?php endif; ?>

                                                    <div class="legend-text">
                                                        <div class="legend-name" title="<?= htmlspecialchars($label) ?>">
                                                            <?= htmlspecialchars($label) ?>
                                                        </div>
                                                        <div class="legend-meta">
                                                            <i class="fas fa-folder" style="font-size:10px;"></i>
                                                            <?= number_format($value) ?> โครงการ
                                                        </div>
                                                    </div>
                                                    <div class="legend-percent"><?= $percent ?>%</div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-chart-pie"></i>
                                    <h4 class="text-muted mb-2">ไม่พบข้อมูลประเภทโครงการ</h4>
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

<script>
// Counter Animation
document.addEventListener('DOMContentLoaded', function(){
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        if(isNaN(target)) return;
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

<?php if (!empty($labels)): ?>
<script>
window.addEventListener('load', function(){

    if(typeof Chart === 'undefined'){
        console.error('Chart.js ไม่ได้ถูกโหลด! กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
        return;
    }

    const ctx = document.getElementById('typeChart');
    if(!ctx) return;

    const chartColors = <?= json_encode($colors) ?>;
    const chartLabels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
    const chartData = <?= json_encode($data) ?>;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
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
            maintainAspectRatio: false,
            cutout: '68%',
            radius: '92%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1800,
                easing: 'easeOutCubic'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 16,
                    cornerRadius: 12,
                    displayColors: true,
                    boxPadding: 6,
                    titleFont: { size: 14, weight: 'bold', family: 'Kanit' },
                    bodyFont: { size: 13, family: 'Kanit' },
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
                                '  จำนวน: ' + context.raw.toLocaleString() + ' โครงการ',
                                '  สัดส่วน: ' + percentage + '%'
                            ];
                        },
                        title: function() { return ''; }
                    }
                }
            }
        }
    });

});
</script>
<?php endif; ?>

</body>
</html>