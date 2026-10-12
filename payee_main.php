<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo '<meta http-equiv="refresh" content="2;URL=index.php" />';
    exit();
}
include "./includes/connect.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>ข้อมูลผู้ขาย/ร้านค้า | C-Smart</title>

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
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        .payee-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .payee-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .payee-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 350px;
            height: 350px;
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
            flex-shrink: 0;
        }
        .hero-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.95rem;
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
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-hero-add {
            background: white;
            color: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.25rem 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            transition: height 0.3s ease;
            background: linear-gradient(90deg, #4f46e5, #4338ca);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        .stat-card:hover::before { height: 6px; }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }
        .stat-card:hover .stat-icon {
            transform: scale(1.08) rotate(-5deg);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.825rem;
            margin: 0;
            font-weight: 500;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1.1;
            margin: 0.15rem 0 0 0;
            letter-spacing: -1px;
        }
        .stat-suffix {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-left: 0.25rem;
        }

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
        }
        .modern-card:hover { box-shadow: var(--shadow-md); }

        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-soft);
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .modern-card .card-header h5 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .card-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .btn-add-modern {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.55rem 1.15rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-add-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
            color: white;
            filter: brightness(1.05);
        }

        .table-wrap { padding: 1.25rem; }
        #example1 { width: 100% !important; }
        #example1 thead th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: #fff !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 0.85rem 0.75rem !important;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        #example1 thead th:first-child { border-top-left-radius: var(--radius-sm); }
        #example1 thead th:last-child  { border-top-right-radius: var(--radius-sm); }
        #example1 tbody td {
            padding: 0.75rem !important;
            vertical-align: middle !important;
            border-color: var(--border-soft) !important;
            font-size: 0.925rem;
        }
        #example1 tbody tr { transition: all 0.2s ease; }
        #example1 tbody tr:hover { background: var(--primary-light) !important; }

        .seq-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.85rem;
        }
        .tax-id {
            display: inline-block;
            font-family: 'Courier New', monospace;
            background: var(--bg-soft);
            color: var(--text-dark);
            padding: 0.3rem 0.7rem;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .payee-name {
            font-weight: 700;
            color: var(--text-dark);
        }
        .payee-name i {
            color: var(--primary);
            margin-right: 0.4rem;
        }
        .address-text {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        .tel-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(135deg, #eef2ff, #fdf2f8);
            color: var(--primary-dark);
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid var(--border-soft);
        }

        .action-cell { white-space: nowrap; }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-action-edit {
            background: white;
            color: var(--primary);
            border-color: #c7d2fe;
        }
        .btn-action-edit:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
        .btn-action-del {
            background: white;
            color: #dc2626;
            border-color: #fecaca;
        }
        .btn-action-del:hover {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-soft);
            padding: 0.4rem 0.75rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            outline: none;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: var(--primary-dark) !important;
            border-color: var(--primary) !important;
        }

        .card-footer-modern {
            background: var(--bg-soft);
            border-top: 1px solid var(--border-soft);
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card, .modern-card { animation: fadeInUp 0.4s ease-out backwards; }
        .stat-card { animation-delay: 0.05s; }
        .modern-card { animation-delay: 0.1s; }

        @media (max-width: 768px) {
            .payee-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .stat-value { font-size: 1.5rem; }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            $totalPayees = 0;
            $school_id = $_SESSION['ss_school_id'];
            try {
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM payee WHERE school_id = :school_id");
                $countStmt->execute(['school_id' => $school_id]);
                $totalPayees = $countStmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("payee_main.php Stats Error: " . $e->getMessage());
            }
            ?>

            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">ข้อมูลผู้ขาย/ร้านค้า</li>
                </ol>
            </nav>

            <div class="payee-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-database"></i> ระบบจัดการข้อมูลผู้ขาย
                            </span>
                            <h1 class="hero-title mt-2">ข้อมูลผู้ขาย/ร้านค้า</h1>
                            <p class="hero-subtitle">จัดการรายชื่อผู้ขาย/ร้านค้าที่ทำธุรกรรมกับสถานศึกษาในระบบ C-Smart</p>
                        </div>
                    </div>
                    <a href="payee_in.php" class="btn-hero-add">
                        <i class="fas fa-plus-circle"></i> เพิ่มผู้ขาย/ร้านค้า
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <p class="stat-label">จำนวนผู้ขาย/ร้านค้าทั้งหมด</p>
                                <div class="stat-value">
                                    <?= number_format($totalPayees) ?><span class="stat-suffix">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="card-header">
                    <h5>
                        <span class="card-header-icon"><i class="fas fa-list"></i></span>
                        รายชื่อผู้ขาย/ร้านค้า
                    </h5>
                    <a href="payee_in.php" class="btn-add-modern">
                        <i class="fas fa-plus"></i> เพิ่มข้อมูล
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table id="example1" class="table align-middle mb-0" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;" class="text-center">ลำดับ</th>
                                        <th style="width: 180px;" class="text-center">เลขประจำตัวผู้เสียภาษี</th>
                                        <th>ชื่อผู้ขาย</th>
                                        <th>ที่อยู่</th>
                                        <th style="width: 140px;" class="text-center">โทรศัพท์</th>
                                        <th style="width: 110px;" class="text-center">ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $a = 0;
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM payee WHERE school_id = :school_id ORDER BY payee_id DESC");
                                        $stmt->execute(['school_id' => $school_id]);
                                        while ($row = $stmt->fetch()) {
                                            $a++;
                                            $payee_id   = $row['payee_id'];
                                            $taxid      = htmlspecialchars($row['taxid']);
                                            $payee_name = htmlspecialchars($row['payee_name']);
                                            $payee_addr = htmlspecialchars($row['payee_address']);
                                            $tel        = htmlspecialchars($row['tel']);
                                            ?>
                                            <tr>
                                                <td class="text-center"><span class="seq-number"><?= $a ?></span></td>
                                                <td class="text-center"><span class="tax-id"><?= $taxid ?></span></td>
                                                <td><span class="payee-name"><i class="fas fa-store-alt"></i><?= $payee_name ?></span></td>
                                                <td><span class="address-text"><i class="fas fa-map-marker-alt me-1 text-muted"></i><?= $payee_addr ?></span></td>
                                                <td class="text-center"><span class="tel-pill"><i class="fas fa-phone"></i><?= $tel ?></span></td>
                                                <td class="text-center action-cell">
                                                    <div class="d-inline-flex gap-2">
                                                        <a href="payee_in.php?submit=EDIT&edit_id=<?= $payee_id ?>" class="btn-action btn-action-edit" title="แก้ไข">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="payee_del.php?submit=DEL&del_id=<?= $payee_id ?>" onclick="return CheckDel()" class="btn-action btn-action-del" title="ลบ">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }

                                        if ($a == 0) {
                                            echo "<tr><td colspan='6'>
                                                    <div class='text-center py-5' style='color: var(--text-muted);'>
                                                        <i class='fas fa-store' style='font-size: 3rem; opacity: 0.4; margin-bottom: 1rem; display: block;'></i>
                                                        <h6 class='fw-semibold mb-1'>ยังไม่มีข้อมูลผู้ขาย/ร้านค้า</h6>
                                                        <p class='small mb-3'>เริ่มต้นด้วยการเพิ่มผู้ขายรายแรกของคุณ</p>
                                                        <a href='payee_in.php' class='btn btn-primary btn-sm'>
                                                            <i class='fas fa-plus me-1'></i> เพิ่มผู้ขาย/ร้านค้า
                                                        </a>
                                                    </div>
                                                  </td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        error_log("payee_main.php Error: " . $e->getMessage());
                                        echo "<tr><td colspan='6' class='text-center text-danger py-4'>
                                                <i class='fas fa-exclamation-triangle me-2'></i>
                                                เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง
                                              </td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer-modern">
                    <i class="fas fa-info-circle me-1"></i> คลิกที่หัวตารางเพื่อเรียงลำดับ · ใช้ช่องค้นหาด้านบนเพื่อค้นหาผู้ขาย
                </div>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    function CheckDel() {
        return confirm('กรุณายืนยันการลบอีกครั้ง !!!');
    }

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
                        searchPlaceholder: "🔍 ค้นหาผู้ขาย/ร้านค้า...",
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
                        { targets: [0, 1, 4, 5], className: 'dt-center' },
                        { targets: [5], orderable: false }
                    ]
                });
            }
        }
    });
</script>
</body>
</html>