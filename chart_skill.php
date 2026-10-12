<!DOCTYPE html>
<html lang="th">
<head>
    <?php
    session_name("C_Smart");
    session_start();

    include "./modules/head.inc.php";
    include "./includes/connect.php";
    ?>

    <title>ภาพรวมทักษะ | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: "Sarabun", "TH Sarabun New", sans-serif;
            background: #f4f7fb;
        }

        .page-hero {
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            border-radius: 24px;
            padding: 28px;
            color: #fff;
            box-shadow: 0 18px 35px rgba(13, 110, 253, 0.22);
            position: relative;
            overflow: hidden;
        }

        .page-hero::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            right: -70px;
            top: -70px;
        }

        .page-hero::before {
            content: "";
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
            right: 120px;
            bottom: -80px;
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
            backdrop-filter: blur(8px);
        }

        .modern-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            transition: all 0.22s ease;
            background: #fff;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        }

        .stat-card {
            cursor: pointer;
            min-height: 145px;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .stat-percent {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 6px;
        }

        .skill-new {
            color: #0d6efd;
        }

        .skill-up {
            color: #198754;
        }

        .skill-re {
            color: #6f42c1;
        }

        .bg-soft-primary {
            background: rgba(13, 110, 253, 0.12);
        }

        .bg-soft-success {
            background: rgba(25, 135, 84, 0.12);
        }

        .bg-soft-purple {
            background: rgba(111, 66, 193, 0.12);
        }

        .bg-soft-dark {
            background: rgba(33, 37, 41, 0.08);
        }

        .chart-container {
            position: relative;
            height: 430px;
            width: 100%;
        }

        .chart-card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7;
            padding: 20px 24px;
        }

        .chart-title {
            font-weight: 800;
            color: #0f172a;
        }

        .chart-subtitle {
            color: #64748b;
            font-size: 0.92rem;
        }

        .breadcrumb-modern .breadcrumb-item a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
        }

        .breadcrumb-modern .breadcrumb-item.active {
            color: #fff;
            font-weight: 600;
        }

        .breadcrumb-modern .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.65);
        }

        .modal-content {
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.2);
        }

        .modal-header {
            border-bottom: 0;
            padding: 20px 24px;
        }

        .modal-body {
            padding: 24px;
        }

        table.dataTable thead th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
        }

        .badge-soft-info {
            background: rgba(13, 202, 240, 0.14);
            color: #087990;
            border: 1px solid rgba(13, 202, 240, 0.25);
        }

        .empty-state {
            padding: 36px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-state i {
            font-size: 42px;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .page-hero {
                padding: 22px;
                border-radius: 20px;
            }

            .stat-number {
                font-size: 1.9rem;
            }

            .chart-container {
                height: 360px;
            }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">

    <?php
    $newSkill = 0;
    $upSkill = 0;
    $reSkill = 0;
    $totalActivities = 0;

    try {
        $sql = "SELECT 
                    COUNT(CASE WHEN skill_id = 1 THEN 1 END) AS new_skill,
                    COUNT(CASE WHEN skill_id = 2 THEN 1 END) AS up_skill,
                    COUNT(CASE WHEN skill_id = 3 THEN 1 END) AS re_skill
                FROM activity";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $newSkill = (int)($row['new_skill'] ?? 0);
        $upSkill = (int)($row['up_skill'] ?? 0);
        $reSkill = (int)($row['re_skill'] ?? 0);
        $totalActivities = $newSkill + $upSkill + $reSkill;

        $newPercent = $totalActivities > 0 ? round($newSkill / $totalActivities * 100, 1) : 0;
        $upPercent = $totalActivities > 0 ? round($upSkill / $totalActivities * 100, 1) : 0;
        $rePercent = $totalActivities > 0 ? round($reSkill / $totalActivities * 100, 1) : 0;

    } catch (Exception $e) {
        error_log("chart_skill.php Error: " . $e->getMessage());
        echo '<div class="container-fluid mt-3">';
        echo '<div class="alert alert-danger rounded-4 shadow-sm">';
        echo '<strong>เกิดข้อผิดพลาด:</strong> ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง';
        echo '</div>';
        echo '</div>';

        $newPercent = 0;
        $upPercent = 0;
        $rePercent = 0;
    }
    ?>

    <div class="app-content-header">
        <div class="container-fluid">

            <div class="page-hero mb-4">
                <div class="row align-items-center position-relative" style="z-index:2;">
                    <div class="col-12 col-lg">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hero-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold">ภาพรวมทักษะในการเรียนรู้</h3>
                                <div class="opacity-75">
                                    กราฟแสดงจำนวนกิจกรรม แยกตามประเภท New Skill, Upskill และ Reskill
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-modern mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">
                                        <i class="fas fa-home me-1"></i> หน้าหลัก
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">
                                    ข้อมูลทักษะ
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="modern-card stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="stat-label mb-2">กิจกรรมทั้งหมด</div>
                                    <div class="stat-number text-dark">
                                        <?= number_format($totalActivities) ?>
                                    </div>
                                    <div class="stat-percent">รวมทุกประเภททักษะ</div>
                                </div>
                                <div class="stat-icon bg-soft-dark text-dark">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="modern-card stat-card" onclick="showSkillDetails(1)">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="stat-label mb-2">New Skill</div>
                                    <div class="stat-number skill-new">
                                        <?= number_format($newSkill) ?>
                                    </div>
                                    <div class="stat-percent">
                                        คิดเป็น <?= $newPercent ?>%
                                    </div>
                                </div>
                                <div class="stat-icon bg-soft-primary text-primary">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: <?= $newPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="modern-card stat-card" onclick="showSkillDetails(2)">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="stat-label mb-2">Upskill</div>
                                    <div class="stat-number skill-up">
                                        <?= number_format($upSkill) ?>
                                    </div>
                                    <div class="stat-percent">
                                        คิดเป็น <?= $upPercent ?>%
                                    </div>
                                </div>
                                <div class="stat-icon bg-soft-success text-success">
                                    <i class="fas fa-arrow-trend-up"></i>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?= $upPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="modern-card stat-card" onclick="showSkillDetails(3)">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="stat-label mb-2">Reskill</div>
                                    <div class="stat-number skill-re">
                                        <?= number_format($reSkill) ?>
                                    </div>
                                    <div class="stat-percent">
                                        คิดเป็น <?= $rePercent ?>%
                                    </div>
                                </div>
                                <div class="stat-icon bg-soft-purple skill-re">
                                    <i class="fas fa-rotate"></i>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar" style="width: <?= $rePercent ?>%; background:#6f42c1;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Chart -->
            <div class="row g-3">
                <div class="col-12 col-xl-8">
                    <div class="modern-card h-100">
                        <div class="chart-card-header">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <div>
                                    <h5 class="chart-title mb-1">
                                        <i class="fas fa-chart-column text-primary me-2"></i>
                                        จำนวนกิจกรรมแยกตามประเภททักษะ
                                    </h5>
                                    <div class="chart-subtitle">
                                        คลิกที่แท่งกราฟเพื่อดูรายการกิจกรรมในแต่ละประเภท
                                    </div>
                                </div>
                                <span class="badge rounded-pill text-bg-light px-3 py-2">
                                    รวม <?= number_format($totalActivities) ?> กิจกรรม
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="chart-container">
                                <canvas id="skillChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Summary -->
                <div class="col-12 col-xl-4">
                    <div class="modern-card h-100">
                        <div class="chart-card-header">
                            <h5 class="chart-title mb-1">
                                <i class="fas fa-circle-info text-primary me-2"></i>
                                สรุปสัดส่วนทักษะ
                            </h5>
                            <div class="chart-subtitle">
                                ภาพรวมเชิงเปอร์เซ็นต์ของกิจกรรมทั้งหมด
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="skill-new">New Skill</strong>
                                    <span><?= $newPercent ?>%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $newPercent ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="skill-up">Upskill</strong>
                                    <span><?= $upPercent ?>%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-success" style="width: <?= $upPercent ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="skill-re">Reskill</strong>
                                    <span><?= $rePercent ?>%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar" style="width: <?= $rePercent ?>%; background:#6f42c1;"></div>
                                </div>
                            </div>

                            <div class="alert alert-primary border-0 rounded-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-lightbulb fa-lg"></i>
                                    </div>
                                    <div>
                                        <strong>คำแนะนำ</strong>
                                        <div class="small mt-1">
                                            สามารถคลิกที่การ์ดหรือแท่งกราฟ เพื่อดูรายละเอียดกิจกรรมในแต่ละประเภทได้ทันที
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Activity Details Modal -->
<div class="modal fade" id="skillModal" tabindex="-1" aria-labelledby="skillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="skillModalLabel">
                    <i class="fas fa-list me-2"></i> รายการกิจกรรม
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="modal-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 mb-0 text-muted">กำลังโหลดข้อมูล...</p>
                </div>

                <div id="modal-content" style="display:none;">
                    <div class="table-responsive">
                        <table id="skillActivityTable" class="table table-striped table-hover align-middle nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:60px;">ลำดับ</th>
                                    <th>ชื่อกิจกรรม</th>
                                    <th>วันที่จัด</th>
                                    <th>สถานที่</th>
                                    <th style="width:110px;">ผู้เข้าร่วม</th>
                                    <th>สถานศึกษา</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-xmark me-1"></i> ปิด
                </button>
            </div>

        </div>
    </div>
</div>

<?php include "./modules/footer.inc.php"; ?>

<script>
    let skillChart = null;
    let skillDataTable = null;

    const skillCounts = {
        newSkill: <?= json_encode($newSkill) ?>,
        upSkill: <?= json_encode($upSkill) ?>,
        reSkill: <?= json_encode($reSkill) ?>
    };

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getSkillName(skillId) {
        if (Number(skillId) === 1) return 'New Skill';
        if (Number(skillId) === 2) return 'Upskill';
        if (Number(skillId) === 3) return 'Reskill';
        return 'ไม่ระบุประเภท';
    }

    function showSkillDetails(skillId) {
        const modalEl = document.getElementById('skillModal');
        const modal = new bootstrap.Modal(modalEl);

        const loadingEl = document.getElementById('modal-loading');
        const contentEl = document.getElementById('modal-content');
        const titleEl = document.getElementById('skillModalLabel');
        const tbody = document.querySelector('#skillActivityTable tbody');

        const skillName = getSkillName(skillId);

        titleEl.innerHTML = `<i class="fas fa-list me-2"></i> กิจกรรมประเภท <strong>${skillName}</strong>`;

        loadingEl.style.display = 'block';
        contentEl.style.display = 'none';
        tbody.innerHTML = '';

        modal.show();

        if (skillDataTable) {
            skillDataTable.destroy();
            skillDataTable = null;
        }

        fetch('get_activity_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'skill_id=' + encodeURIComponent(skillId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            loadingEl.style.display = 'none';
            contentEl.style.display = 'block';

            tbody.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0 || data.error) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-folder-open d-block"></i>
                                ไม่พบข้อมูลกิจกรรมสำหรับประเภทนี้
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach((activity, index) => {
                const placeText = `${activity.place || ''} ${activity.address || ''}`.trim();

                const row = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <strong>${escapeHtml(activity.activity_name || '-')}</strong>
                        </td>
                        <td class="text-center">
                            ${escapeHtml(activity.date_range_display || '-')}
                        </td>
                        <td>
                            ${escapeHtml(placeText || '-')}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-soft-info rounded-pill px-3 py-2">
                                ${escapeHtml(activity.total_participants || 0)}
                            </span>
                        </td>
                        <td>
                            ${escapeHtml(activity.school_name || '-')}
                        </td>
                    </tr>
                `;

                tbody.insertAdjacentHTML('beforeend', row);
            });

            skillDataTable = $('#skillActivityTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'asc']],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        })
        .catch(error => {
            loadingEl.style.display = 'none';
            contentEl.style.display = 'block';

            tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="alert alert-danger rounded-4 mb-0">
                            <strong>เกิดข้อผิดพลาด:</strong> ${escapeHtml(error.message)}
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    function drawChart() {
        const canvas = document.getElementById('skillChart');

        if (!canvas) {
            console.error('ไม่พบ canvas id="skillChart"');
            return;
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js ยังไม่ถูกโหลด กรุณาตรวจสอบ script CDN');
            return;
        }

        const ctx = canvas.getContext('2d');

        if (skillChart) {
            skillChart.destroy();
        }

        const gradientNew = ctx.createLinearGradient(0, 0, 0, 420);
        gradientNew.addColorStop(0, 'rgba(13, 110, 253, 0.95)');
        gradientNew.addColorStop(1, 'rgba(13, 110, 253, 0.35)');

        const gradientUp = ctx.createLinearGradient(0, 0, 0, 420);
        gradientUp.addColorStop(0, 'rgba(25, 135, 84, 0.95)');
        gradientUp.addColorStop(1, 'rgba(25, 135, 84, 0.35)');

        const gradientRe = ctx.createLinearGradient(0, 0, 0, 420);
        gradientRe.addColorStop(0, 'rgba(111, 66, 193, 0.95)');
        gradientRe.addColorStop(1, 'rgba(111, 66, 193, 0.35)');

        skillChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['New Skill', 'Upskill', 'Reskill'],
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: [
                        skillCounts.newSkill,
                        skillCounts.upSkill,
                        skillCounts.reSkill
                    ],
                    backgroundColor: [
                        gradientNew,
                        gradientUp,
                        gradientRe
                    ],
                    borderColor: [
                        '#0d6efd',
                        '#198754',
                        '#6f42c1'
                    ],
                    borderWidth: 1,
                    borderRadius: 14,
                    borderSkipped: false,
                    barThickness: 72,
                    maxBarThickness: 90
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.raw + ' กิจกรรม';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '10%',
                        grid: {
                            color: '#edf2f7',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            color: '#64748b',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const skillIds = [1, 2, 3];
                        showSkillDetails(skillIds[index]);
                    }
                },
                onHover: function(event, chartElement) {
                    event.native.target.style.cursor = chartElement.length ? 'pointer' : 'default';
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        drawChart();
    });
</script>

</body>
</html>