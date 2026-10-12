<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id']) || $_SESSION['ss_login_status'] != 2) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
}

include "./includes/connect.php";
include "./includes/function.php";

$act_id = $_GET['act_id'] ?? null;

if (empty($act_id)) {
    echo "Invalid activity ID.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>ตรวจสอบโครงการ | C-Smart</title>

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

        /* ── Detail Table ── */
        .detail-table {
            margin-bottom: 0;
            border-radius: 12px;
            overflow: hidden;
        }
        .detail-table td {
            vertical-align: middle;
            padding: 12px 14px;
        }
        .detail-table td:first-child {
            font-weight: 600;
            background-color: #f8fafc;
            width: 180px;
            white-space: nowrap;
            color: #1e293b;
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

        /* ── Gender Badge ── */
        .gender-male {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .gender-female {
            background: #fce7f3;
            color: #db2777;
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
            .detail-table td:first-child {
                width: 120px;
                font-size: 0.85rem;
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
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fw-bold">ตรวจสอบโครงการ</h4>
                        <p class="mb-0 small opacity-75">รายละเอียดข้อมูลกิจกรรม/โครงการที่ขออนุมัติ</p>
                    </div>
                    <div class="d-none d-md-block">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small" style="background:rgba(255,255,255,0.15);padding:6px 16px;border-radius:50px;">
                                <li class="breadcrumb-item"><a href="index.php" class="text-white opacity-75">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="approve_main.php" class="text-white opacity-75">อนุมัติ</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">ตรวจสอบ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- ═══ Activity Detail Card ═══ -->
            <div class="row">
                <div class="col-12">
                    <div class="card modern-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i> รายละเอียดโครงการ
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered detail-table">
                                <tbody>
                                    <?php
                                    $id_school_ch = substr($_SESSION['ss_school_id'], 0, 6);
                                    $activity_name = '-';
                                    $course_name = '-';
                                    $course_hour = '-';
                                    $culture_name = '-';
                                    $format_name_show = '-';
                                    $attribute_name = '-';
                                    $type_name = '-';
                                    $firstname = '-';
                                    $lastname = '-';
                                    $place = '-';
                                    $address = '-';
                                    $latitude = '-';
                                    $longtitude = '-';
                                    $time_start = '-';
                                    $time_end = '-';
                                    $total_study = 0;
                                    $men_study = 0;
                                    $women_study = 0;
                                    $date = '-';

                                    try {
                                        $sql = "SELECT activity.activity_name, 
                                                       course.course_name, course.course_hour, 
                                                       culture.culture_name, format.format_name_show, 
                                                       attribute.attribute_name, type.type_name, 
                                                       lecturer.firstname, lecturer.lastname, 
                                                       activity.place, activity.address, 
                                                       activity.latitude, activity.longtitude, 
                                                       activity.date_id, activity.date_range, 
                                                       activity.date_start, activity.date_end, 
                                                       activity.time_start, activity.time_end, 
                                                       activity.total_study
                                                FROM activity 
                                                LEFT JOIN attribute ON activity.attribute_id = attribute.attribute_id 
                                                LEFT JOIN course ON activity.course_id = course.course_id 
                                                LEFT JOIN format ON activity.format_id = format.format_id 
                                                LEFT JOIN lecturer ON activity.lecturer_id = lecturer.lecturer_id 
                                                LEFT JOIN culture ON culture.culture_id = course.culture_id 
                                                LEFT JOIN type ON activity.type_id = type.type_id 
                                                WHERE activity.activity_id = :act_id AND activity.school_id LIKE :id_school_ch LIMIT 1";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([':act_id' => $act_id, ':id_school_ch' => $id_school_ch . '%']);
                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                        if ($row) {
                                            $activity_name = $row['activity_name'] ?? '-';
                                            $course_name = $row['course_name'] ?? '-';
                                            $course_hour = $row['course_hour'] ?? '-';
                                            $culture_name = $row['culture_name'] ?? '-';
                                            $format_name_show = $row['format_name_show'] ?? '-';
                                            $attribute_name = $row['attribute_name'] ?? '-';
                                            $type_name = $row['type_name'] ?? '-';
                                            $firstname = $row['firstname'] ?? '-';
                                            $lastname = $row['lastname'] ?? '-';
                                            $place = $row['place'] ?? '-';
                                            $address = $row['address'] ?? '-';
                                            $latitude = $row['latitude'] ?? '-';
                                            $longtitude = $row['longtitude'] ?? '-';
                                            $time_start = $row['time_start'] ?? '-';
                                            $time_end = $row['time_end'] ?? '-';
                                            $total_study = $row['total_study'] ?? 0;

                                            if ($row['date_id'] == "1") {
                                                if ($row['date_start'] == $row['date_end']) {
                                                    $date = ADYearToBEYearTH($row['date_start'], 2);
                                                } else {
                                                    $date = ADYearToBEYearTH($row['date_start'], 2) . " ถึง " . ADYearToBEYearTH($row['date_end'], 2);
                                                }
                                            } else {
                                                $date = DateRangeToString($row['date_range'], 2);
                                            }

                                            // Count male/female students
                                            $sql2 = "SELECT COUNT(IF(sex_id='1', 1, NULL)) as men_study, 
                                                            COUNT(IF(sex_id='2', 1, NULL)) as women_study 
                                                     FROM train 
                                                     INNER JOIN student ON train.student_id = student.student_id 
                                                     WHERE train.activity_id = :act_id2";
                                            $stmt2 = $pdo->prepare($sql2);
                                            $stmt2->execute([':act_id2' => $act_id]);
                                            $count = $stmt2->fetch(PDO::FETCH_ASSOC);
                                            $men_study = $count['men_study'] ?? 0;
                                            $women_study = $count['women_study'] ?? 0;
                                        }
                                    } catch (PDOException $e) {
                                        error_log("approve_detail.php Detail Error: " . $e->getMessage());
                                    }
                                    ?>

                                    <?php if ($activity_name !== '-'): ?>
                                        <tr>
                                            <td><i class="fas fa-tag me-2 text-primary opacity-75"></i> ชื่อโครงการ</td>
                                            <td colspan="3" class="fw-semibold"><?= htmlspecialchars($activity_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-book me-2 text-primary opacity-75"></i> หลักสูตร</td>
                                            <td colspan="3"><?= htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($course_hour, ENT_QUOTES, 'UTF-8') ?> ชั่วโมง)</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-briefcase me-2 text-primary opacity-75"></i> กลุ่มอาชีพ</td>
                                            <td colspan="3"><?= htmlspecialchars($culture_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-calendar-check me-2 text-primary opacity-75"></i> รูปแบบการจัด</td>
                                            <td colspan="3"><?= htmlspecialchars($format_name_show, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-clipboard-list me-2 text-primary opacity-75"></i> ลักษณะโครงการ</td>
                                            <td colspan="3"><?= htmlspecialchars($attribute_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-layer-group me-2 text-primary opacity-75"></i> ประเภทโครงการ</td>
                                            <td colspan="3"><?= htmlspecialchars($type_name, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-chalkboard-teacher me-2 text-primary opacity-75"></i> วิทยากร</td>
                                            <td colspan="3"><?= htmlspecialchars($firstname . ' ' . $lastname, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-map-marker-alt me-2 text-primary opacity-75"></i> สถานที่จัด</td>
                                            <td><?= htmlspecialchars($place, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-satellite me-2 text-primary opacity-75"></i> GPS</td>
                                            <td>
                                                ละติจูด: <?= htmlspecialchars($latitude, ENT_QUOTES, 'UTF-8') ?> | ลองจิจูด: <?= htmlspecialchars($longtitude, ENT_QUOTES, 'UTF-8') ?>
                                                <a href="https://www.google.co.th/maps/search/<?= urlencode($latitude) ?>,<?= urlencode($longtitude) ?>" target="_blank" class="ms-2 text-decoration-none" data-bs-toggle="tooltip" title="เปิด Google Maps">
                                                    <i class="fas fa-map-marked-alt text-success"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-home me-2 text-primary opacity-75"></i> ที่อยู่</td>
                                            <td><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-calendar-day me-2 text-primary opacity-75"></i> วันที่ดำเนินการ</td>
                                            <td><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-clock me-2 text-primary opacity-75"></i> เวลาดำเนินการ</td>
                                            <td><?= htmlspecialchars($time_start, ENT_QUOTES, 'UTF-8') ?> น. ถึง <?= htmlspecialchars($time_end, ENT_QUOTES, 'UTF-8') ?> น.</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-users me-2 text-primary opacity-75"></i> ผู้เรียน</td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 me-2">ทั้งหมด <?= (int)$total_study ?> คน</span>
                                                <span class="badge gender-male rounded-pill px-3 py-1 me-2"><i class="fas fa-male me-1"></i> ชาย <?= (int)$men_study ?> คน</span>
                                                <span class="badge gender-female rounded-pill px-3 py-1"><i class="fas fa-female me-1"></i> หญิง <?= (int)$women_study ?> คน</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <div class="empty-state-icon mb-3"><i class="fas fa-exclamation-circle"></i></div>
                                                <span class="fs-5">ไม่พบข้อมูลโครงการ</span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ═══ Student List Card ═══ -->
                    <div class="card modern-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i> รายชื่อผู้เข้าร่วมโครงการ
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="studentTable" class="table table-bordered table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="20px">ลำดับ</th>
                                            <th class="text-center">ชื่อ-สกุล</th>
                                            <th class="text-center" width="80px">เพศ</th>
                                            <th class="text-center" width="80px">อายุ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $sql = "SELECT student.student_id, student.firstname, 
                                                           student.lastname, sex.sex_name, 
                                                           student.birthday 
                                                    FROM train 
                                                    INNER JOIN student ON train.student_id = student.student_id 
                                                    INNER JOIN sex ON sex.sex_id = student.sex_id 
                                                    WHERE train.activity_id = :act_id AND student.school_id LIKE :id_school_ch
                                                    ORDER BY student.student_id DESC";
                                            $stmt = $pdo->prepare($sql);
                                            $stmt->execute([':act_id' => $act_id, ':id_school_ch' => $id_school_ch . '%']);
                                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if ($students) {
                                                $a = 0;
                                                foreach ($students as $row) {
                                                    $a++;
                                                    $age = BirthDateAge($row['birthday']);
                                                    $sex = $row['sex_name'] ?? '-';
                                                    $genderClass = ($sex == 'ชาย') ? 'gender-male' : 'gender-female';
                                                    echo "<tr>";
                                                    echo "<td class='text-center'>$a</td>";
                                                    echo "<td>" . htmlspecialchars($row['firstname'] . '  ' . $row['lastname'], ENT_QUOTES, 'UTF-8') . "</td>";
                                                    echo "<td class='text-center'><span class='badge $genderClass rounded-pill px-3 py-1'>" . htmlspecialchars($sex, ENT_QUOTES, 'UTF-8') . "</span></td>";
                                                    echo "<td class='text-center'>$age</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>
                                                        <div class='empty-state-icon mb-3'><i class='fas fa-user-graduate'></i></div>
                                                        <span class='fs-5'>ไม่มีผู้เข้าร่วมโครงการ</span>
                                                      </td></tr>";
                                            }
                                        } catch (PDOException $e) {
                                            error_log("approve_detail.php Student Error: " . $e->getMessage());
                                            echo "<tr><td colspan='4' class='text-center text-danger py-4'>
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

                    <!-- ═══ Action Buttons ═══ -->
                    <div class="text-center mt-4 mb-4">
                        <a href="approve_main.php" class="btn btn-outline-secondary px-4 me-2" style="border-radius:12px;">
                            <i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายการ
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script>
    $(document).ready(function() {
        // Only init DataTable when there are real data rows
        var hasRealRows = $('#studentTable tbody tr').length > 0 &&
                          !$('#studentTable tbody td[colspan]').length;

        if (hasRealRows) {
            $('#studentTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "ทั้งหมด"]],
                order: [],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 ค้นหาผู้เรียน...",
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
                    { targets: [0, 2, 3], className: 'dt-center' }
                ]
            });
        }

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    });
</script>
</body>
</html>
