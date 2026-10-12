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

    <title>ข้อมูลกิจกรรม/โครงการ | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Sarabun", "TH Sarabun New", sans-serif;
            background: #f4f7fb;
        }

        /* ── Page Hero ── */
        .page-hero {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            border-radius: 24px;
            padding: 28px;
            color: #fff;
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

        /* ── Cards ── */
        .modern-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            transition: all 0.25s ease;
            overflow: hidden;
        }
        .modern-card:hover {
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
            transform: translateY(-2px);
        }
        .modern-card .card-header {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            color: #fff;
            border-bottom: none;
            padding: 16px 22px;
        }
        .modern-card .card-header .card-title {
            font-weight: 600;
        }

        .stat-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.06);
            transition: all 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.1);
        }
        .stat-number {
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }
        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* ── School Table ── */
        #schoolTable thead th {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            color: #fff;
            font-weight: 600;
            border-bottom: none;
            padding: 12px 10px;
        }
        #schoolTable tbody td {
            padding: 10px 10px;
            vertical-align: middle;
        }
        #schoolTable tbody tr:hover {
            background-color: #f0f7ff !important;
        }
        .school-name {
            font-weight: 500;
        }
        .action-btn {
            min-width: 108px;
        }
        .total-row {
            background-color: #e8f5e9 !important;
            font-weight: 700;
        }
        .total-row td {
            border-top: 2px solid #4caf50 !important;
        }
        .badge-count {
            padding: 4px 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-folder-open text-primary me-2"></i>
                        ข้อมูลกิจกรรม/โครงการ
                    </h4>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active">ข้อมูลกิจกรรม/โครงการ</li>
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
            // Pre-fetch data for stats + clean rendering
            $schools = [];
            $total_activity = 0;
            $total_study = 0;
            $total_schools = 0;

            try {
                $stmt = $pdo->query("SELECT * FROM school WHERE status='2' ORDER BY school_name ASC");
                $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($schools as $row) {
                    $id_school_ch = substr($row['school_id'], 0, 6);

                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE school_id LIKE :school_id");
                    $stmt2->execute(['school_id' => $id_school_ch . '%']);
                    $num_rows = (int)$stmt2->fetchColumn();

                    $stmt3 = $pdo->prepare("SELECT COALESCE(SUM(total_study),0) AS total_study FROM activity WHERE school_id LIKE :school_id");
                    $stmt3->execute(['school_id' => $id_school_ch . '%']);
                    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                    $study_count = (int)$row3['total_study'];

                    $schools[$total_schools]['_num_activity'] = $num_rows;
                    $schools[$total_schools]['_total_study'] = $study_count;

                    $total_activity += $num_rows;
                    $total_study += $study_count;
                    $total_schools++;
                }
            } catch (PDOException $e) {
                error_log("act_view_all.php Error: " . $e->getMessage());
                echo '<div class="alert alert-danger rounded-4 shadow-sm">เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง</div>';
            }
            ?>

            <!-- Page Hero -->
            <div class="page-hero mb-4">
                <div class="page-hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small opacity-75">ภาพรวมกิจกรรม / โครงการ</div>
                            <h4 class="mb-0 fw-bold">ข้อมูลกิจกรรม/โครงการ</h4>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <div class="small opacity-75">อำเภอที่จัดกิจกรรม</div>
                            <span class="badge bg-white bg-opacity-25 fs-6 px-3 py-1 rounded-pill">
                                <?= number_format($total_schools) ?> แห่ง
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stat Cards -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <div class="stat-label">สถานศึกษาที่จัดกิจกรรม</div>
                                <div class="stat-number text-primary"><?= number_format($total_schools) ?></div>
                                <div class="small text-muted">แห่ง</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div>
                                <div class="stat-label">โครงการ/กิจกรรมทั้งหมด</div>
                                <div class="stat-number text-success"><?= number_format($total_activity) ?></div>
                                <div class="small text-muted">โครงการ</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-info bg-opacity-10 text-info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-label">ผู้เรียนทั้งหมด</div>
                                <div class="stat-number text-info"><?= number_format($total_study) ?></div>
                                <div class="small text-muted">คน</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card modern-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0 d-flex align-items-center">
                                    <i class="fas fa-list-ul me-2"></i>
                                    <span>รายชื่อสถานศึกษา</span>
                                    <span class="badge bg-white bg-opacity-25 ms-2"><?= number_format($total_schools) ?> แห่ง</span>
                                </div>
                                <div class="small opacity-75 d-none d-md-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    คลิก "ดูรายละเอียด" เพื่อดูโครงการของแต่ละสถานศึกษา
                                </div>
                            </div>
                        </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="schoolTable" class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60px;" class="text-center">ลำดับ</th>
                                                    <th>สถานศึกษา</th>
                                                    <th style="width: 130px;" class="text-center">จำนวนโครงการ</th>
                                                    <th style="width: 130px;" class="text-center">จำนวนผู้เรียน</th>
                                                    <th style="width: 150px;" class="text-center">การดำเนินการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($schools)): ?>
                                                    <?php foreach ($schools as $index => $row): ?>
                                                        <tr>
                                                            <td class="text-center fw-medium"><?= $index + 1 ?></td>
                                                            <td>
                                                                <span class="school-name"><?= htmlspecialchars($row['school_name']) ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-count bg-success bg-opacity-10 text-success"><?= number_format($row['_num_activity']) ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-count bg-info bg-opacity-10 text-info"><?= number_format($row['_total_study']) ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="act_view_district.php?id=<?= urlencode($row['school_name']) ?>" 
                                                                   class="btn btn-primary btn-sm action-btn">
                                                                    <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5 text-muted">
                                                            <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                                                            <span class="fs-5">ไม่พบข้อมูลสถานศึกษา</span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="total-row">
                                                    <td class="text-end fw-bold" colspan="2">
                                                        <i class="fas fa-calculator me-1"></i> รวมทั้งหมด
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="fs-5 text-success fw-bold"><?= number_format($total_activity) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="fs-5 text-info fw-bold"><?= number_format($total_study) ?></span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-light small text-muted px-3 py-2">
                                    <i class="fas fa-sync-alt me-1"></i> ข้อมูล ณ ปัจจุบัน • คลิกหัวตารางเพื่อเรียงลำดับ • ค้นหาสถานศึกษาจากช่องค้นหาด้านบน
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.container-fluid -->
            </div> <!-- /.app-content -->
        </main> <!-- /.app-main -->

        <?php include "./modules/footer.inc.php"; ?>

    </div> <!-- /.app-wrapper -->

    <script>
        $(function () {
            // Bootstrap Tooltip
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });

            const table = $('#schoolTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
                order: [[0, 'asc']],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 ค้นหาสถานศึกษา...",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                },
                columnDefs: [
                    { targets: [0, 2, 3, 4], className: 'dt-center' },
                    { targets: [4], orderable: false }
                ]
            });

            // Focus search box on page load
            setTimeout(function() {
                $('#schoolTable_filter input').focus();
            }, 300);
        });
    </script>
</body>
</html>