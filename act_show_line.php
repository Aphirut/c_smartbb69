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
    include "./includes/function.php";

    $act_id = $_GET['act_id'] ?? null;

    if (empty($act_id)) {
        echo "Invalid activity ID.";
        exit;
    }
    ?>

    <title>รายละเอียดกิจกรรม | C-Smart</title>

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
        .modern-card .card-header h5 {
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

        /* ── Detail Info ── */
        .detail-label {
            font-weight: 500;
            color: #6c757d;
            min-width: 160px;
            padding-right: 1rem;
        }
        .detail-value {
            font-weight: 600;
            color: #212529;
        }
        .info-section {
            border-bottom: 1px solid #f1f3f5;
            padding: 12px 0;
        }
        .info-section:last-child {
            border-bottom: none;
        }

        /* ── Student Table ── */
        #studentTable thead th {
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            color: #fff;
            font-weight: 600;
            border-bottom: none;
            padding: 12px 10px;
        }
        #studentTable tbody td {
            padding: 10px 10px;
            vertical-align: middle;
        }
        #studentTable tbody tr:hover {
            background-color: #f0f7ff !important;
        }

        /* ── Badge helpers ── */
        .badge-male {
            background: #dbeafe;
            color: #2563eb;
            padding: 4px 14px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .badge-female {
            background: #fce7f3;
            color: #db2777;
            padding: 4px 14px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <?php
    $activity = null;
    $students = [];
    $latitude = $longtitude = '';
    $date = '';
    $total_students = 0;
    $men = 0;
    $women = 0;
    $error_message = '';

    try {
        // Activity details
        $sql = "SELECT activity.activity_id, activity.activity_name, 
                course.course_name, course.course_hour, 
                culture.culture_name, format.format_name_show, 
                attribute.attribute_name, type.type_name, 
                lecturer.firstname, lecturer.lastname, 
                activity.place, activity.address, 
                activity.latitude, activity.longtitude, 
                activity.date_id, activity.date_range, 
                activity.date_start, activity.date_end, 
                activity.time_start, activity.time_end, 
                activity.total_study, activity.men_study,
                activity.women_study
                FROM activity 
                LEFT JOIN attribute ON activity.attribute_id = attribute.attribute_id 
                LEFT JOIN course ON activity.course_id = course.course_id 
                LEFT JOIN format ON activity.format_id = format.format_id 
                LEFT JOIN lecturer ON activity.lecturer_id = lecturer.lecturer_id 
                LEFT JOIN culture ON culture.culture_id = course.culture_id 
                LEFT JOIN type ON activity.type_id = type.type_id 
                WHERE activity.activity_id = :act_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':act_id' => $act_id]);
        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($activity) {
            $latitude = $activity['latitude'];
            $longtitude = $activity['longtitude'];

            if ($activity['date_id'] == "1") {
                if ($activity['date_start'] == $activity['date_end']) {
                    $date = ADYearToBEYearTH($activity['date_start'], 2);
                } else {
                    $date = ADYearToBEYearTH($activity['date_start'], 2) . " ถึง " . ADYearToBEYearTH($activity['date_end'], 2);
                }
            } else {
                $date = DateRangeToString($activity['date_range'], 2);
            }

            $men = (int)($activity['men_study'] ?? 0);
            $women = (int)($activity['women_study'] ?? 0);
            $total_students = (int)($activity['total_study'] ?? 0);

            // Student list
            $sql2 = "SELECT student.student_id, student.firstname, student.lastname, 
                            sex.sex_name, student.birthday 
                     FROM train 
                     INNER JOIN student ON train.student_id = student.student_id 
                     INNER JOIN sex ON sex.sex_id = student.sex_id 
                     WHERE train.activity_id = :act_id
                     ORDER BY student.firstname ASC, student.lastname ASC";

            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([':act_id' => $act_id]);
            $students = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("act_show_line.php Error: " . $e->getMessage());
        $error_message = 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง';
    }
    ?>

    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        รายละเอียดกิจกรรม
                    </h4>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="act_main.php">กิจกรรม</a></li>
                            <li class="breadcrumb-item active">รายละเอียด</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm"><?= $error_message ?></div>
            <?php elseif (!$activity): ?>
                <div class="alert alert-warning rounded-4 shadow-sm">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ไม่พบข้อมูลกิจกรรมที่ต้องการ (act_id = <?= htmlspecialchars($act_id) ?>)
                </div>
            <?php else: ?>

            <!-- Page Hero -->
            <div class="page-hero mb-4">
                <div class="page-hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small opacity-75">ชื่อโครงการ / กิจกรรม</div>
                            <h4 class="mb-0 fw-bold"><?= htmlspecialchars($activity['activity_name']) ?></h4>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <div class="small opacity-75">รหัสกิจกรรม</div>
                            <span class="badge bg-white bg-opacity-25 fs-6 px-3 py-1 rounded-pill">
                                #<?= htmlspecialchars($act_id) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-label">ผู้เรียนทั้งหมด</div>
                                <div class="stat-number text-primary"><?= number_format($total_students) ?></div>
                                <div class="small text-muted">คน</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-info bg-opacity-10 text-info">
                                <i class="fas fa-male"></i>
                            </div>
                            <div>
                                <div class="stat-label">ชาย</div>
                                <div class="stat-number text-info"><?= number_format($men) ?></div>
                                <div class="small text-muted">คน</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-female"></i>
                            </div>
                            <div>
                                <div class="stat-label">หญิง</div>
                                <div class="stat-number text-danger"><?= number_format($women) ?></div>
                                <div class="small text-muted">คน</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3">
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="stat-label">วันที่จัด</div>
                                <div class="stat-number text-success" style="font-size:1.2rem;"><?= htmlspecialchars($date) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Details Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card modern-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <span>ข้อมูลรายละเอียดกิจกรรม</span>
                            </h5>
                        </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-lg-6">
                                        <div class="info-section d-flex">
                                            <div class="detail-label">ชื่อโครงการ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['activity_name']) ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">หลักสูตร</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['course_name'] ?? '-') ?> (<?= $activity['course_hour'] ?? '0' ?> ชั่วโมง)</div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">กลุ่มอาชีพ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['culture_name'] ?? '-') ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">รูปแบบการจัด</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['format_name_show'] ?? '-') ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">ลักษณะโครงการ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['attribute_name'] ?? '-') ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">ประเภทโครงการ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['type_name'] ?? '-') ?></div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-lg-6">
                                        <div class="info-section d-flex">
                                            <div class="detail-label">วิทยากร</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars(trim(($activity['firstname'] ?? '') . ' ' . ($activity['lastname'] ?? ''))) ?: '-' ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">สถานที่จัด</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['place'] ?? '-') ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">ที่อยู่</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['address'] ?? '-') ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">วันที่ดำเนินการ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($date) ?></div>
                                        </div>
                                        <div class="info-section d-flex">
                                            <div class="detail-label">เวลาดำเนินการ</div>
                                            <div class="detail-value flex-grow-1"><?= htmlspecialchars($activity['time_start'] ?? '-') ?> น. ถึง <?= htmlspecialchars($activity['time_end'] ?? '-') ?> น.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3">
                                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                                </a>
                                <div class="d-flex gap-2">
                                    <?php if (!empty($latitude) && !empty($longtitude)): ?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?= $latitude ?>,<?= $longtitude ?>" 
                                           target="_blank" class="btn btn-info btn-sm rounded-pill px-3">
                                            <i class="fas fa-map-marker-alt me-1"></i> เปิดแผนที่ Google
                                        </a>
                                    <?php endif; ?>
                                    <a href="act_show_line.php?act_id=<?= $act_id ?>" class="btn btn-light btn-sm rounded-pill px-3" title="รีเฟรช">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participants List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card modern-card">
                            <div class="card-header">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="fas fa-user-friends me-2"></i>
                                    <span>รายชื่อผู้เข้าร่วม</span>
                                    <span class="badge bg-white bg-opacity-25 ms-2"><?= count($students) ?> คน</span>
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table id="studentTable" class="table table-striped table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;" class="text-center">ลำดับ</th>
                                                <th>ชื่อ - สกุล</th>
                                                <th style="width: 100px;" class="text-center">เพศ</th>
                                                <th style="width: 90px;" class="text-center">อายุ (ปี)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($students) > 0): ?>
                                                <?php foreach ($students as $index => $student): ?>
                                                    <?php $age = BirthDateAge($student['birthday']); ?>
                                                    <tr>
                                                        <td class="text-center fw-medium"><?= $index + 1 ?></td>
                                                        <td class="fw-medium"><?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?></td>
                                                        <td class="text-center">
                                                            <?php if ($student['sex_name'] === 'ชาย'): ?>
                                                                <span class="badge-male"><?= $student['sex_name'] ?></span>
                                                            <?php else: ?>
                                                                <span class="badge-female"><?= $student['sex_name'] ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="fw-semibold"><?= $age ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">
                                                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                                        ไม่มีข้อมูลผู้เข้าร่วม
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-light small text-muted px-3 py-2">
                                <i class="fas fa-info-circle me-1"></i> คลิกหัวตารางเพื่อเรียงลำดับ • ค้นหาชื่อผู้เข้าร่วมจากช่องค้นหาด้านบน
                            </div>
                        </div>
                    </div>
                </div>

                <?php endif; ?>
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

        // Only init DataTable when there are real data rows (prevent colspan parse error)
        var hasRealRows = $('#studentTable tbody tr').length > 0 &&
                          !$('#studentTable tbody td[colspan]').length;

        if (hasRealRows) {
            $('#studentTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
                order: [],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 ค้นหาชื่อผู้เข้าร่วม...",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบผู้เข้าร่วมที่ตรงกับคำค้นหา",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                },
                columnDefs: [
                    { targets: [0, 2, 3], className: 'dt-center' }
                ]
            });
        }
    });
</script>
</body>
</html>