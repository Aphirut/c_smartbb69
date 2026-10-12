<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id']) || $_SESSION['ss_login_status'] != 2) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
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
    <title>อนุมัติกิจกรรม/โครงการ | C-Smart</title>

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

        /* ── Badge Count ── */
        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0 8px;
            background: rgba(255, 255, 255, 0.15);
        }

        /* ── Table ── */
        #activityTable thead th {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            color: #fff;
            font-weight: 600;
            border-bottom: none;
            padding: 12px 10px;
        }
        #activityTable tbody td {
            padding: 10px 10px;
            vertical-align: middle;
        }
        #activityTable tbody tr:hover {
            background-color: #f0f7ff !important;
        }
        .action-btn {
            padding: 4px 10px;
            font-size: 0.85rem;
            border-radius: 10px;
        }
        .action-btn:hover {
            transform: translateY(-1px);
        }

        /* ── Empty State ── */
        .empty-state-icon {
            font-size: 3.5rem;
            opacity: 0.4;
        }

        /* ── DataTable Search ── */
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .page-hero {
                padding: 20px;
                border-radius: 16px;
            }
            .hero-icon {
                width: 46px;
                height: 46px;
                font-size: 22px;
            }
            .stat-number {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">

            <!-- ═══ Page Hero ═══ -->
            <div class="page-hero mb-4">
                <div class="page-hero-content d-flex align-items-center gap-3">
                    <div class="hero-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold">อนุมัติกิจกรรม/โครงการ</h4>
                        <p class="mb-0 small opacity-75">ตรวจสอบและอนุมัติการจัดตั้งกลุ่มกิจกรรม</p>
                    </div>
                    <div class="d-none d-md-block">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small" style="background:rgba(255,255,255,0.15);padding:6px 16px;border-radius:50px;">
                                <li class="breadcrumb-item"><a href="index.php" class="text-white opacity-75">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">อนุมัติ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- ═══ Stat Cards ═══ -->
            <?php
            $pendingCount = 0;
            $approvedCount = 0;
            $totalCount = 0;
            try {
                $id_school_ch = substr($_SESSION['ss_school_id'], 0, 6);
                $stmtCount = $pdo->prepare("SELECT status_line, COUNT(*) as cnt FROM activity WHERE school_id LIKE :sch GROUP BY status_line");
                $stmtCount->execute([':sch' => $id_school_ch . '%']);
                $counts = $stmtCount->fetchAll(PDO::FETCH_ASSOC);
                foreach ($counts as $c) {
                    $totalCount += (int)$c['cnt'];
                    if ($c['status_line'] == "") {
                        $pendingCount += (int)$c['cnt'];
                    } else {
                        $approvedCount += (int)$c['cnt'];
                    }
                }
            } catch (PDOException $e) {
                error_log("approve_main.php Stat Count Error: " . $e->getMessage());
            }
            ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box" style="background:#eef2ff;color:#2563eb;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div>
                                <div class="stat-number"><?= $totalCount ?></div>
                                <div class="stat-label">กิจกรรมทั้งหมด</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box" style="background:#fef3c7;color:#d97706;">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                <div class="stat-number"><?= $pendingCount ?></div>
                                <div class="stat-label">รออนุมัติ</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box" style="background:#ecfdf5;color:#059669;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-number"><?= $approvedCount ?></div>
                                <div class="stat-label">อนุมัติแล้ว</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Activity Table ═══ -->
            <div class="row">
                <div class="col-12">
                    <div class="card modern-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i> รายการรออนุมัติ
                            </h5>
                            <?php if ($pendingCount > 0): ?>
                                <span class="badge-count">
                                    <i class="fas fa-clock me-1"></i> <?= $pendingCount ?> รายการ
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <table id="activityTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="20px">ลำดับ</th>
                                        <th class="text-center" width="160px">วันที่</th>
                                        <th class="text-center">ชื่อโครงการ</th>
                                        <th class="text-center">ผู้จัดโครงการ</th>
                                        <th class="text-center" width="50px">ผู้เรียน</th>
                                        <th class="text-center" width="130px">ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $a = 0;
                                    $id_school_ch = substr($_SESSION['ss_school_id'], 0, 6);

                                    try {
                                        $sql = "SELECT activity.activity_id, activity.status_line, 
                                                    activity.date_id, activity.date_range, 
                                                    activity.date_start, activity.date_end, 
                                                    activity.total_study, activity.activity_name,  
                                                    school.school_name 
                                                FROM activity 
                                                INNER JOIN school ON activity.school_id = school.school_id 
                                                WHERE activity.school_id LIKE :id_school_ch ORDER BY activity_id DESC";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([':id_school_ch' => $id_school_ch . '%']);
                                        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if ($activities) {
                                            foreach ($activities as $row) {
                                                $a++;
                                                $date = '-';
                                                if ($row['date_id'] == "1") {
                                                    if ($row['date_start'] == $row['date_end']) {
                                                        $date = ADYearToBEYearTH($row['date_start'], 1);
                                                    } else {
                                                        $date = ADYearToBEYearTH($row['date_start'], 1) . " ถึง " . ADYearToBEYearTH($row['date_end'], 1);
                                                    }
                                                } else {
                                                    $date = DateRangeToString($row['date_range'], 1);
                                                }
                                                echo "<tr>";
                                                echo "<td class='text-center'>$a</td>";
                                                echo "<td class='text-center'>$date</td>";
                                                echo "<td class='text-start fw-medium'>{$row['activity_name']}</td>";
                                                echo "<td class='text-start'>{$row['school_name']}</td>";
                                                echo "<td class='text-center'><span class='badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1'>{$row['total_study']}</span></td>";
                                                echo "<td class='text-center text-nowrap'>";
                                                echo "<a href='approve_detail.php?act_id={$row['activity_id']}' class='btn btn-sm btn-outline-primary action-btn me-1' data-bs-toggle='tooltip' title='ตรวจสอบรายละเอียด'><i class='fas fa-search'></i></a>";
                                                if ($row['status_line'] == "") {
                                                    echo "<a href='approve_send_telegram.php?act_id={$row['activity_id']}' onclick='return CheckConfirm();' class='btn btn-sm btn-outline-success action-btn me-1' data-bs-toggle='tooltip' title='อนุมัติจัดตั้งกลุ่ม'><i class='fab fa-telegram'></i></a>";
                                                    echo "<span class='btn btn-sm btn-outline-danger action-btn disabled' data-bs-toggle='tooltip' title='ยังไม่อนุมัติ'><i class='fas fa-exclamation-triangle'></i></span>";
                                                } else {
                                                    echo "<span class='btn btn-sm btn-outline-success action-btn me-1' data-bs-toggle='tooltip' title='อนุมัติแล้ว'><i class='fab fa-telegram'></i></span>";
                                                    echo "<span class='btn btn-sm btn-outline-success action-btn' data-bs-toggle='tooltip' title='อนุมัติจัดตั้งกลุ่มแล้ว'><i class='fas fa-check-circle'></i></span>";
                                                }
                                                echo "</td></tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>
                                                    <div class='empty-state-icon mb-3'><i class='fas fa-inbox'></i></div>
                                                    <span class='fs-5'>ไม่มีรายการรออนุมัติ</span>
                                                  </td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        error_log("approve_main.php Error: " . $e->getMessage());
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
            </div>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script type="text/javascript">
    function CheckConfirm() {
        return confirm('กรุณายืนยันการอนุมัติการจัดตั้งกลุ่มอีกครั้ง !!!');
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Only init DataTable when there are real data rows
        var hasRealRows = $('#activityTable tbody tr').length > 0 &&
                          !$('#activityTable tbody td[colspan]').length;

        if (hasRealRows) {
            $('#activityTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ทั้งหมด"]],
                order: [],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 ค้นหาโครงการ...",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
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

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    });
</script>
</body>
</html>
