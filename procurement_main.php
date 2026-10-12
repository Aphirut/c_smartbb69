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
    <title>ข้อมูลจัดซื้อจัดจ้าง | C-Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(15,23,42,0.08);
            --shadow-lg: 0 10px 40px rgba(79,70,229,0.15);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #f5f7ff 0%, #f8fafc 100%);
        }

        /* ===== Modern Breadcrumb ===== */
        .modern-breadcrumb {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }
        .modern-breadcrumb .breadcrumb { margin: 0; }
        .modern-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        /* ===== Hero Section ===== */
        .page-hero {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(245,158,11,0.25);
            margin-bottom: 1.5rem;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -50%; left: -10%;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            position: relative; z-index: 2;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .hero-avatar {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            border: 2px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .hero-title {
            font-size: 1.6rem; font-weight: 700;
            margin: 0; letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.95rem; opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .hero-stat {
            background: white;
            color: #ef4444;
            padding: 0.85rem 1.5rem;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .hero-stat .stat-number {
            font-size: 1.6rem;
            line-height: 1;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-stat .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===== Summary Stat Card ===== */
        .stat-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #ef4444, #ec4899);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .stat-card-body {
            display: flex; align-items: center; gap: 1rem;
        }
        .stat-icon-box {
            width: 60px; height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(245,158,11,0.3);
        }
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
            letter-spacing: -0.5px;
        }
        .stat-unit {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* ===== Modern Card (Table Container) ===== */
        .modern-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
            z-index: 1;
        }
        .modern-card:hover { box-shadow: var(--shadow-md); }
        .modern-card .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--border-soft);
            padding: 1.1rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
            color: var(--text-dark) !important;
        }
        .modern-card .card-header h5 {
            margin: 0;
            font-size: 1.05rem; font-weight: 700;
            color: var(--text-dark);
            display: flex; align-items: center; gap: 0.6rem;
        }
        .card-header-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }

        /* ===== Modern Table ===== */
        .modern-table {
            margin: 0 !important;
            font-size: 0.9rem;
        }
        .modern-table thead th,
        #example1 thead th,
        #example1_wrapper thead th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            font-weight: 700 !important;
            border: none !important;
            padding: 0.85rem 0.75rem !important;
            font-size: 0.875rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .modern-table thead th:first-child {
            border-top-left-radius: 0;
        }
        .modern-table thead th:last-child {
            border-top-right-radius: 0;
        }
        .modern-table tbody tr {
            transition: all 0.2s ease;
        }
        .modern-table tbody tr:hover {
            background: linear-gradient(90deg, #eef2ff 0%, #fae8ff 100%) !important;
            transform: scale(1.002);
        }
        .modern-table tbody td {
            padding: 0.85rem 0.75rem !important;
            vertical-align: middle;
            color: var(--text-dark);
            border-color: var(--border-soft) !important;
        }
        .seq-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #eef2ff, #fae8ff);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
        }
        .date-badge {
            display: inline-block;
            padding: 0.3rem 0.7rem;
            background: rgba(79,70,229,0.08);
            color: var(--primary);
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 600;
        }
        .project-name {
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.5;
        }

        /* ===== Action Button ===== */
        .action-cell {
            white-space: nowrap;
            width: 80px;
            text-align: center;
        }
        .action-btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white !important;
            border: none;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            box-shadow: 0 3px 10px rgba(245,158,11,0.25);
            text-decoration: none;
        }
        .action-btn-modern:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 14px rgba(245,158,11,0.4);
            color: white !important;
        }

        /* ===== Card Footer Info ===== */
        .modern-card .card-footer {
            background: var(--bg-soft) !important;
            border-top: 1px solid var(--border-soft) !important;
            padding: 0.85rem 1.5rem !important;
            font-size: 0.825rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .modern-card .card-footer i {
            color: var(--primary);
        }

        /* ===== DataTables Customization ===== */
        .dataTables_wrapper {
            padding: 1rem 1.5rem 0.5rem;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 0.4rem 0.75rem;
            margin-left: 0.5rem;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79,70,229,0.15);
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 0.35rem 0.5rem;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.8rem;
            color: var(--text-muted);
            padding: 0.75rem 0;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.75rem 0;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            margin: 0 2px;
            border: 1px solid var(--border-soft) !important;
            padding: 0.35rem 0.75rem !important;
            font-size: 0.85rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            border-color: transparent !important;
            box-shadow: 0 3px 8px rgba(79,70,229,0.3);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        /* ===== Empty State ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 3rem;
            color: var(--border-soft);
            margin-bottom: 1rem;
        }

        /* ===== Animation ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card, .modern-card, .page-hero {
            animation: fadeInUp 0.4s ease-out backwards;
        }
        .page-hero { animation-delay: 0s; }
        .stat-card { animation-delay: 0.1s; }
        .modern-card { animation-delay: 0.2s; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .page-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.2rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .stat-value { font-size: 1.5rem; }
            .stat-icon-box { width: 50px; height: 50px; font-size: 1.2rem; }
            .modern-table { font-size: 0.825rem; }
            .modern-table thead th,
            .modern-table tbody td { padding: 0.6rem 0.5rem !important; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- ===== Breadcrumb ===== -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold">ข้อมูลจัดซื้อจัดจ้าง</li>
                </ol>
            </nav>

            <?php
            // Summary stats
            $totalProjects = 0;
            try {
                $school_id = $_SESSION['ss_school_id'];
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE school_id = :school_id");
                $stmt->execute(['school_id' => $school_id]);
                $totalProjects = $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("procurement_main.php Stats Error: " . $e->getMessage());
            }
            ?>

            <!-- ===== Hero Section ===== -->
            <div class="page-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <span class="hero-badge">
                                <i class="fas fa-file-invoice-dollar"></i> ระบบจัดซื้อจัดจ้าง
                            </span>
                            <h1 class="hero-title mt-2">ข้อมูลจัดซื้อจัดจ้าง</h1>
                            <p class="hero-subtitle">บันทึกและจัดการข้อมูลการจัดซื้อจัดจ้างของแต่ละโครงการ</p>
                        </div>
                    </div>
                    <div class="hero-stat">
                        <div>
                            <div class="stat-number"><?= number_format($totalProjects) ?></div>
                            <div class="stat-label">โครงการทั้งหมด</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Summary Card ===== -->
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-card-body">
                            <div class="stat-icon-box">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="stat-label">จำนวนโครงการทั้งหมด</div>
                                <div class="stat-value"><?= number_format($totalProjects) ?></div>
                                <div class="stat-unit">โครงการ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Main Table Card ===== -->
            <div class="modern-card">
                <div class="card-header">
                    <h5>
                        <span class="card-header-icon"><i class="fas fa-list"></i></span>
                        รายการโครงการที่ต้องบันทึกจัดซื้อจัดจ้าง
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="example1" class="table modern-table align-middle mb-0" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 70px;" class="text-center">ลำดับ</th>
                                    <th style="width: 180px;" class="text-center">วันที่</th>
                                    <th>ชื่อ-โครงการ</th>
                                    <th style="width: 90px;" class="text-center">บันทึก</th>
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
                                            WHERE activity.school_id = :school_id
                                            ORDER BY activity_id DESC";
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
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="seq-badge"><?= $a ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="date-badge">
                                                    <i class="far fa-calendar-alt me-1"></i><?= $date ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="project-name">
                                                    <?= htmlspecialchars($row['activity_name']) ?>
                                                </div>
                                            </td>
                                            <td class="action-cell">
                                                <a href="procurement_show.php?act_id=<?= $row['activity_id'] ?>"
                                                   class="action-btn-modern"
                                                   data-bs-toggle="tooltip"
                                                   title="บันทึกข้อมูลจัดซื้อจัดจ้าง">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }

                                    if ($a == 0) {
                                        echo '<tr><td colspan="4">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    <div>ยังไม่มีโครงการในระบบ</div>
                                                </div>
                                              </td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("procurement_main.php Error: " . $e->getMessage());
                                    echo '<tr><td colspan="4" class="text-danger text-center p-4">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
                                          </td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <i class="fas fa-info-circle"></i>
                    <span>คลิกหัวตารางเพื่อเรียงลำดับ • ใช้ช่องค้นหาด้านบนเพื่อกรองข้อมูล</span>
                </div>
            </div>

        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->
</main> <!-- /.app-main -->

<?php include "./modules/footer.inc.php"; ?>

<script>
    $(function () {
        // เปิดใช้งาน Tooltip
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });

        // DataTable (only when there are real data rows)
        if (typeof $.fn.DataTable !== 'undefined') {
            var hasRealRows = $('#example1 tbody tr').length > 0 &&
                              !$('#example1 tbody td[colspan]').length;

            if (hasRealRows) {
                $('#example1').DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 15,
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
        }
    });
</script>
</body>
</html>