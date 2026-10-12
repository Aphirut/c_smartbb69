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

    <title>Dashboard ข้อมูลโครงการ | C-Smart</title>

    <!-- Google Fonts: Kanit + Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        :root{
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --info: #06b6d4;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --gray: #64748b;
            --light: #f8fafc;
        }

        body{
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf5 100%);
            font-family: 'Kanit', 'Sarabun', sans-serif;
            min-height: 100vh;
        }

        /* Animated Background Pattern */
        body::before{
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(6, 182, 212, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .app-main{
            position: relative;
            z-index: 1;
        }

        /* === Header === */
        .app-content-header{
            padding-top: 30px;
            padding-bottom: 20px;
        }

        .dashboard-title{
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1.5px;
            margin-bottom: 8px;
        }

        .dashboard-title i{
            -webkit-text-fill-color: #6366f1;
        }

        .dashboard-subtitle{
            color: #64748b;
            font-size: 15px;
            font-weight: 400;
        }

        /* === Summary Cards === */
        .summary-card{
            border: none;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #fff;
            min-height: 200px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .summary-card:hover{
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.20);
        }

        .summary-card::before{
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            top: -80px;
            right: -80px;
            transition: all .5s ease;
        }

        .summary-card::after{
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            bottom: -40px;
            left: -40px;
            transition: all .5s ease;
        }

        .summary-card:hover::before{
            transform: scale(1.2) rotate(45deg);
        }

        .summary-card:hover::after{
            transform: scale(1.3);
        }

        .summary-primary{
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .summary-success{
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .summary-info{
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .summary-body{
            padding: 32px;
            position: relative;
            z-index: 10;
        }

        .summary-icon{
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            font-size: 26px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: all .3s ease;
        }

        .summary-card:hover .summary-icon{
            transform: rotate(-10deg) scale(1.1);
            background: rgba(255, 255, 255, 0.35);
        }

        .summary-label{
            margin-top: 24px;
            font-size: 14px;
            font-weight: 500;
            opacity: .95;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-number{
            font-size: 52px;
            font-weight: 800;
            line-height: 1;
            margin-top: 12px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .summary-small{
            font-size: 13px;
            opacity: .9;
            margin-top: 8px;
            font-weight: 300;
        }

        /* === Main Card === */
        .modern-card{
            border: none;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .modern-card-header{
            padding: 32px 36px 16px;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .modern-card-title{
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-card-title i{
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
        }

        .badge-total{
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .modern-card-body{
            padding: 20px 36px 36px;
        }

        /* === Chart === */
        .chart-container{
            position: relative;
            height: 680px;
            background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
            border-radius: 20px;
            padding: 20px;
        }

        /* === Ranking === */
        .ranking-title{
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ranking-title i{
            color: #f59e0b;
        }

        .ranking-box{
            max-height: 660px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .ranking-box::-webkit-scrollbar{
            width: 6px;
        }

        .ranking-box::-webkit-scrollbar-track{
            background: #f1f5f9;
            border-radius: 20px;
        }

        .ranking-box::-webkit-scrollbar-thumb{
            background: linear-gradient(180deg, #6366f1, #4f46e5);
            border-radius: 20px;
        }

        .ranking-item{
            position: relative;
            padding: 16px;
            border-radius: 16px;
            background: #fff;
            margin-bottom: 12px;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .ranking-item::before{
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--bar-color, #6366f1);
            transition: width .3s ease;
        }

        .ranking-item:hover{
            transform: translateX(6px) translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .ranking-item:hover::before{
            width: 6px;
        }

        /* Top 3 medals */
        .rank-medal{
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: #fff;
            margin-right: 12px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .rank-1{
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .rank-2{
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
        }

        .rank-3{
            background: linear-gradient(135deg, #fb923c, #ea580c);
        }

        .rank-other{
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            color: #475569;
        }

        .ranking-top{
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .ranking-info{
            display: flex;
            align-items: center;
            min-width: 0;
            flex: 1;
        }

        .school-details{
            min-width: 0;
            flex: 1;
        }

        .school-name{
            font-weight: 600;
            color: #1e293b;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
            line-height: 1.3;
        }

        .school-percent{
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
            font-weight: 500;
        }

        .school-count{
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            flex-shrink: 0;
        }

        .school-count-label{
            font-size: 11px;
            color: #94a3b8;
            font-weight: 400;
            text-align: right;
            margin-top: -2px;
        }

        /* Progress Bar */
        .progress-track{
            height: 6px;
            background: #f1f5f9;
            border-radius: 50px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill{
            height: 100%;
            border-radius: 50px;
            background: linear-gradient(90deg, var(--bar-color, #6366f1), var(--bar-color-2, #4f46e5));
            transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
        }

        /* === Breadcrumb === */
        .breadcrumb{
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a{
            color: #64748b;
            text-decoration: none;
            transition: color .2s;
            font-weight: 500;
        }

        .breadcrumb-item a:hover{
            color: #4f46e5;
        }

        .breadcrumb-item.active{
            color: #4f46e5;
            font-weight: 600;
        }

        /* === Empty State === */
        .empty-state{
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i{
            font-size: 80px;
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

        .summary-card,
        .modern-card{
            animation: fadeInUp .6s ease-out both;
        }

        .summary-card:nth-child(1){ animation-delay: .1s; }
        .summary-card:nth-child(2){ animation-delay: .2s; }
        .summary-card:nth-child(3){ animation-delay: .3s; }

        .modern-card{
            animation-delay: .4s;
        }

        /* === Responsive === */
        @media (max-width: 768px){
            .dashboard-title{
                font-size: 26px;
            }
            .summary-number{
                font-size: 38px;
            }
            .summary-body{
                padding: 24px;
            }
            .chart-container{
                height: 480px;
                padding: 10px;
            }
            .school-name{
                max-width: 140px;
            }
            .modern-card-body{
                padding: 16px 20px 24px;
            }
            .modern-card-header{
                padding: 24px 20px 12px;
            }
        }

    </style>

</head>

<body class="layout-fixed sidebar-expand-lg">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">

        <div class="container-fluid">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <h1 class="dashboard-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Dashboard ข้อมูลโครงการ
                    </h1>

                    <div class="dashboard-subtitle">
                        <i class="fas fa-info-circle me-1"></i>
                        ระบบแสดงข้อมูลจำนวนโครงการแยกตามสถานศึกษา
                    </div>

                </div>

                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">

                    <ol class="breadcrumb justify-content-lg-end mb-0">

                        <li class="breadcrumb-item">
                            <a href="index.php">
                                <i class="fas fa-home me-1"></i>
                                หน้าหลัก
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Dashboard
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>

    <!-- Content -->
    <div class="app-content">

        <div class="container-fluid">

<?php

$labels = [];
$data = [];

$totalProjects = 0;
$schoolsWithActivity = 0;

$colors = [
    '#6366f1', '#10b981', '#f59e0b', '#ef4444',
    '#8b5cf6', '#06b6d4', '#f97316', '#ec4899',
    '#14b8a6', '#6b7280', '#3b82f6', '#84cc16',
    '#0ea5e9', '#d946ef', '#22c55e', '#eab308'
];

try {

    $sql_school = "
        SELECT *
        FROM school
        WHERE status='2'
        ORDER BY school_name ASC
    ";

    $stmt_school = $pdo->query($sql_school);

    $tempData = [];

    while($row_school = $stmt_school->fetch()) {

        $id_school_ch = substr($row_school['school_id'], 0, 6);

        $sql_activity = "
            SELECT COUNT(*) AS number
            FROM activity
            WHERE school_id LIKE :school_id
        ";

        $stmt_activity = $pdo->prepare($sql_activity);

        $stmt_activity->execute([
            'school_id' => $id_school_ch . '%'
        ]);

        $row_activity = $stmt_activity->fetch();

        $count = (int)$row_activity['number'];

        if($count > 0){

            $tempData[] = [
                'name' => $row_school['school_name'],
                'count' => $count
            ];

            $totalProjects += $count;
            $schoolsWithActivity++;

        }

    }

    usort($tempData, function($a, $b){
        return $b['count'] <=> $a['count'];
    });

    foreach($tempData as $item){
        $labels[] = $item['name'];
        $data[] = $item['count'];
    }

    $maxValue = !empty($data) ? max($data) : 0;

} catch(PDOException $e){
    error_log("chart_activity_number.php Error: " . $e->getMessage());

    echo '
    <div class="alert alert-danger rounded-4 shadow-sm">
        เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
    </div>
    ';

}

?>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">

                <!-- Card 1 -->
                <div class="col-lg-4 col-md-6">

                    <div class="summary-card summary-primary">

                        <div class="summary-body">

                            <div class="summary-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>

                            <div class="summary-label">
                                <i class="fas fa-arrow-up me-1"></i>
                                โครงการทั้งหมด
                            </div>

                            <div class="summary-number" data-count="<?= $totalProjects ?>">
                                0
                            </div>

                            <div class="summary-small">
                                จำนวนโครงการทั้งหมดในระบบ
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Card 2 -->
                <div class="col-lg-4 col-md-6">

                    <div class="summary-card summary-success">

                        <div class="summary-body">

                            <div class="summary-icon">
                                <i class="fas fa-school"></i>
                            </div>

                            <div class="summary-label">
                                <i class="fas fa-check-circle me-1"></i>
                                สถานศึกษาที่มีโครงการ
                            </div>

                            <div class="summary-number" data-count="<?= $schoolsWithActivity ?>">
                                0
                            </div>

                            <div class="summary-small">
                                จำนวนสถานศึกษาที่ดำเนินโครงการ
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Card 3 -->
                <div class="col-lg-4 col-md-12">

                    <div class="summary-card summary-info">

                        <div class="summary-body">

                            <div class="summary-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>

                            <div class="summary-label">
                                <i class="fas fa-balance-scale me-1"></i>
                                เฉลี่ยต่อสถานศึกษา
                            </div>

                            <div class="summary-number">
                                <?= ($schoolsWithActivity > 0)
                                    ? number_format($totalProjects / $schoolsWithActivity, 1)
                                    : 0 ?>
                            </div>

                            <div class="summary-small">
                                ค่าเฉลี่ยโครงการต่อแห่ง
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Main Chart -->
            <div class="modern-card">

                <div class="modern-card-header">

                    <div class="modern-card-title">
                        <i class="fas fa-chart-bar"></i>
                        จำนวนโครงการแยกตามสถานศึกษา
                    </div>

<?php if(!empty($labels)): ?>
                    <div class="badge-total">
                        <i class="fas fa-database me-1"></i>
                        ทั้งหมด <?= number_format($schoolsWithActivity) ?> แห่ง
                    </div>
<?php endif; ?>

                </div>

                <div class="modern-card-body">

<?php if(!empty($labels)): ?>

                    <div class="row g-4">

                        <!-- Chart -->
                        <div class="col-lg-8">

                            <div class="chart-container">
                                <canvas id="activityChart"></canvas>
                            </div>

                        </div>

                        <!-- Ranking -->
                        <div class="col-lg-4">

                            <div class="ranking-title">
                                <i class="fas fa-trophy"></i>
                                อันดับสถานศึกษา
                            </div>

                            <div class="ranking-box">

<?php

$rank = 1;

foreach($labels as $index => $school){

    $count = $data[$index];
    $color = $colors[$index % count($colors)];

    $percent = ($totalProjects > 0)
        ? round(($count / $totalProjects) * 100, 1)
        : 0;

    $widthPercent = ($maxValue > 0)
        ? round(($count / $maxValue) * 100, 1)
        : 0;

    if($rank == 1){
        $medalClass = 'rank-1';
        $medalIcon = '<i class="fas fa-crown"></i>';
    } elseif($rank == 2){
        $medalClass = 'rank-2';
        $medalIcon = '<i class="fas fa-medal"></i>';
    } elseif($rank == 3){
        $medalClass = 'rank-3';
        $medalIcon = '<i class="fas fa-award"></i>';
    } else {
        $medalClass = 'rank-other';
        $medalIcon = $rank;
    }

?>

                                <div class="ranking-item" style="--bar-color: <?= $color ?>; --bar-color-2: <?= $color ?>;">

                                    <div class="ranking-top">

                                        <div class="ranking-info">

                                            <div class="rank-medal <?= $medalClass ?>">
                                                <?= $medalIcon ?>
                                            </div>

                                            <div class="school-details">

                                                <div class="school-name"
                                                     title="<?= htmlspecialchars($school) ?>">
                                                    <?= htmlspecialchars($school) ?>
                                                </div>

                                                <div class="school-percent">
                                                    <i class="fas fa-percentage" style="font-size:10px;"></i>
                                                    <?= $percent ?>% ของทั้งหมด
                                                </div>

                                            </div>

                                        </div>

                                        <div>
                                            <div class="school-count">
                                                <?= number_format($count) ?>
                                            </div>
                                            <div class="school-count-label">
                                                โครงการ
                                            </div>
                                        </div>

                                    </div>

                                    <div class="progress-track">
                                        <div class="progress-fill"
                                             style="width: <?= $widthPercent ?>%;">
                                        </div>
                                    </div>

                                </div>

<?php

$rank++;

}

?>

                            </div>

                        </div>

                    </div>

<?php else: ?>

                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h4 class="text-muted mb-2">ไม่พบข้อมูลโครงการ</h4>
                        <p class="text-secondary">ยังไม่มีสถานศึกษาที่ดำเนินโครงการในระบบ</p>
                    </div>

<?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include "./modules/footer.inc.php"; ?>

<!-- Counter Animation -->
<script>
document.addEventListener('DOMContentLoaded', function(){

    const counters = document.querySelectorAll('.summary-number[data-count]');

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

<?php if(!empty($labels)): ?>

const ctx = document.getElementById('activityChart');

const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
const dataValues = <?= json_encode($data) ?>;
const colors = <?= json_encode($colors) ?>;

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: labels,

        datasets: [{

            label: 'จำนวนโครงการ',
            data: dataValues,

            backgroundColor: function(context){

                const chart = context.chart;
                const {ctx, chartArea} = chart;

                if(!chartArea){
                    return '#6366f1';
                }

                const gradient = ctx.createLinearGradient(
                    chartArea.left, 0, chartArea.right, 0
                );

                gradient.addColorStop(0, '#a5b4fc');
                gradient.addColorStop(0.5, '#6366f1');
                gradient.addColorStop(1, '#4f46e5');

                return gradient;

            },

            borderRadius: {
                topRight: 14,
                bottomRight: 14,
                topLeft: 4,
                bottomLeft: 4
            },
            borderSkipped: false,
            borderWidth: 0,
            hoverBackgroundColor: '#4338ca',
            barPercentage: 0.75,
            categoryPercentage: 0.85

        }]

    },

    options: {

        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,

        animation: {
            duration: 1800,
            easing: 'easeOutQuart',
            delay: (context) => context.dataIndex * 60
        },

        layout: {
            padding: {
                right: 30,
                left: 5,
                top: 10,
                bottom: 5
            }
        },

        plugins: {

            legend: {
                display: false
            },

            title: {
                display: true,
                text: '📊 กราฟแสดงจำนวนโครงการแยกตามสถานศึกษา',
                font: {
                    size: 18,
                    weight: 'bold',
                    family: 'Kanit'
                },
                color: '#1e293b',
                padding: {
                    top: 10,
                    bottom: 30
                }
            },

            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                padding: 16,
                cornerRadius: 12,
                displayColors: false,
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
                borderColor: 'rgba(99, 102, 241, 0.5)',
                borderWidth: 1,
                callbacks: {
                    label: function(context){
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.raw / total) * 100).toFixed(1);
                        return [
                            '📁 จำนวน : ' + context.raw.toLocaleString() + ' โครงการ',
                            '📊 สัดส่วน : ' + percentage + '%'
                        ];
                    }
                }
            },

            datalabels: {
                display: false
            }

        },

        scales: {

            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(226, 232, 240, 0.6)',
                    drawBorder: false
                },
                ticks: {
                    precision: 0,
                    color: '#64748b',
                    font: {
                        size: 11,
                        family: 'Kanit'
                    }
                },
                title: {
                    display: true,
                    text: 'จำนวนโครงการ (รายการ)',
                    color: '#475569',
                    font: {
                        size: 13,
                        weight: 'bold',
                        family: 'Kanit'
                    },
                    padding: {top: 10}
                }
            },

            y: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    color: '#1e293b',
                    font: {
                        size: 12,
                        weight: '500',
                        family: 'Kanit'
                    },
                    callback: function(value, index){
                        const label = this.getLabelForValue(value);
                        return label.length > 25 ? label.substring(0, 25) + '...' : label;
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