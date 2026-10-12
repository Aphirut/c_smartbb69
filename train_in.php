<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
     exit;
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php
        include "./modules/head.inc.php";
        include "./includes/connect.php";
        include "./includes/function.php";

         $act_id = filter_input(INPUT_GET, 'act_id', FILTER_VALIDATE_INT);
			
			
            if ($act_id === false || $act_id === null) {
                header("Location: index.php");
                exit;
            }

         $sql = "SELECT COUNT(*) FROM activity WHERE activity_id = :act_id AND school_id = :school_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                 header("Location: index.php");
                exit;
            }
        ?>
        <style>
            .train-card {
                border: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
                border-radius: 12px;
                overflow: hidden;
            }
            .train-card .card-header {
                background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
                color: white;
                padding: 1rem 1.25rem;
            }
            #studentTable thead th,
            .student-select-table thead th,
            #studentTable_wrapper .dataTables_scrollHead th {
                background-color: #0d6efd !important;
                color: #ffffff !important;
                font-weight: 600;
            }
            .student-select-table tbody tr:hover {
                background-color: #f0f7ff;
            }
            .form-check-input {
                width: 1.2em;
                height: 1.2em;
            }
            .info-summary td {
                padding: 0.5rem 0.75rem;
            }
        </style>
    </head>

    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <!-- Content Header (AdminLTE 4 style) -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        จัดการผู้เรียน
                    </h4>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="act_main.php">ข้อมูลกิจกรรม/โครงการ</a></li>
                            <li class="breadcrumb-item"><a href="train_show.php?act_id=<?= $act_id ?>">รายละเอียดโครงการ</a></li>
                            <li class="breadcrumb-item active">จัดการผู้เรียน</li>
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
                        // Fetch activity summary
                        $sql = "SELECT a.activity_name, c.course_name, c.course_hour, 
                                       a.total_study, a.date_id, a.date_range, 
                                       a.date_start, a.date_end, a.time_start, a.time_end 
                                FROM activity a
                                INNER JOIN course c ON a.course_id = c.course_id 
                                WHERE a.activity_id = :act_id AND a.school_id = :school_id LIMIT 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
                        $row = $stmt->fetch();

                        $activity_name = $row['activity_name'] ?? '';
                        $course_name = $row['course_name'] ?? '';
                        $course_hour = $row['course_hour'] ?? '';
                        $time_start = $row['time_start'] ?? '';
                        $time_end = $row['time_end'] ?? '';
                        $total_study = $row['total_study'] ?? 0;

                        if (($row['date_id'] ?? '') == "1") {
                            if (($row['date_start'] ?? '') == ($row['date_end'] ?? '')) {
                                $date = ADYearToBEYearTH($row['date_start'], 2);
                            } else {
                                $date = ADYearToBEYearTH($row['date_start'], 2) . " ถึง " . ADYearToBEYearTH($row['date_end'], 2);
                            }
                        } else {
                            $date = DateRangeToString($row['date_range'] ?? '', 2);
                        }

                        // Current enrolled count
                        $sql_count = "SELECT COUNT(IF(s.sex_id='1', 1, NULL)) AS men_study, 
                                             COUNT(IF(s.sex_id='2', 1, NULL)) AS women_study 
                                      FROM train t
                                      INNER JOIN student s ON t.student_id = s.student_id 
                                      WHERE t.activity_id = :act_id AND s.school_id = :school_id";
                        $stmt_count = $pdo->prepare($sql_count);
                        $stmt_count->execute(['act_id' => $act_id, 'school_id' => $_SESSION['ss_school_id']]);
                        $row_count = $stmt_count->fetch();
                        $men_study = $row_count['men_study'] ?? 0;
                        $women_study = $row_count['women_study'] ?? 0;
                        ?>

            <!-- Activity Summary -->
            <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong class="text-muted">ชื่อโครงการ</strong><br>
                                                <span class="fs-5 fw-semibold"><?= htmlspecialchars($activity_name) ?></span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong class="text-muted">หลักสูตร</strong><br>
                                                <?= htmlspecialchars($course_name) ?> (<?= $course_hour ?> ชั่วโมง)
                                            </div>
                                            <div class="col-md-4">
                                                <strong class="text-muted">วันที่ / เวลา</strong><br>
                                                <?= htmlspecialchars($date) ?> | <?= htmlspecialchars($time_start) ?> - <?= htmlspecialchars($time_end) ?> น.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

            <!-- Main Form Card -->
            <div class="row">
                            <div class="col-12">
                                <div class="card train-card shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                <i class="fas fa-users me-2"></i>
                                                เลือกผู้เรียนที่ต้องการเพิ่มเข้าร่วมโครงการ
                                            </h5>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="train_show.php?act_id=<?= $act_id ?>" class="btn btn-outline-light btn-sm">
                                                <i class="fas fa-arrow-left me-1"></i> กลับไปรายละเอียด
                                            </a>
                                        </div>
                                    </div>

                                    <form action="train_save.php" method="post">
                                        <div class="card-body">
                                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                                <div>
                                                    <strong>วิธีการใช้งาน:</strong> ทำเครื่องหมายถูกในช่อง "เลือก" สำหรับผู้เรียนที่ต้องการเพิ่มเข้าร่วมโครงการนี้
                                                </div>
                                            </div>

                                            <!-- Student Selection Table -->
                                            <div class="table-responsive">
                                                <table id="studentTable" class="table table-striped table-hover student-select-table align-middle" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 60px;" class="text-center">ลำดับ</th>
                                                            <th>ชื่อ-สกุล</th>
                                                            <th style="width: 130px;" class="text-center">เลขบัตรประชาชน</th>
                                                            <th style="width: 80px;" class="text-center">เพศ</th>
                                                            <th style="width: 80px;" class="text-center">เลือก</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $a = 0;
                                                    $sql = "SELECT s.student_id,  
                                                        s.firstname, s.lastname, 
                                                        s.idcard, sx.sex_name, 
                                                        t.train_id, t.a_score, 
                                                        t.b_score, t.c_score, 
                                                        t.total_scores, t.status_assess
                                                        FROM activity a
                                                        LEFT JOIN student s ON a.school_id = s.school_id 
                                                        LEFT JOIN train t ON s.student_id = t.student_id AND a.activity_id = t.activity_id 
                                                        LEFT JOIN sex sx ON sx.sex_id = s.sex_id 
                                                        WHERE a.activity_id = :act_id
                                                        ORDER BY s.student_id DESC";
                                                    $stmt = $pdo->prepare($sql);
                                                    $stmt->execute(['act_id' => $act_id]);

                                                    while ($row = $stmt->fetch()) {
                                                        if (!empty($row['firstname'])) {
                                                            $a++;
                                                            $isChecked = !empty($row['train_id']) ? 'checked' : '';
                                                            ?>
                                                            <tr>
                                                                <td class="text-center fw-medium"><?= $a ?></td>
                                                                <td><strong><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></strong></td>
                                                                <td class="text-center font-monospace"><?= htmlspecialchars($row['idcard']) ?></td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-<?= ($row['sex_name'] == 'ชาย' ? 'primary' : 'danger') ?>">
                                                                        <?= htmlspecialchars($row['sex_name']) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input cb-student-select" type="checkbox" 
                                                                               data-student-id="<?= $row['student_id'] ?>" 
                                                                               value="<?= $row['student_id'] ?>" 
                                                                               <?= $isChecked ?>>
                                                                    </div>
                                                                </td>

                                                                <!-- Hidden fields moved outside the table to fix DataTables column count -->
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>

                                    <div class="card-footer bg-light py-3">
                                        <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-save me-2"></i> บันทึกการเลือกผู้เรียน
                                            </button>
                                            <a href="train_show.php?act_id=<?= $act_id ?>" class="btn btn-secondary px-4">
                                                <i class="fas fa-times me-2"></i> ยกเลิก
                                            </a>
                                            <input type="hidden" name="act_id" value="<?= $act_id ?>">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

            </div> <!-- /.container-fluid -->
        </div> <!-- /.app-content -->
    </main> <!-- /.app-main -->

        <?php include "./modules/footer.inc.php"; ?>

    </div> <!-- /.app-wrapper -->

    <!-- DataTables -->

    <script>
        $(function () {
            // เก็บรายชื่อนักเรียนที่ถูกเลือกไว้ (แก้ปัญหา DataTables search แล้ว DOM หาย)
            var selectedStudents = new Set();

            function syncCheckboxState() {
                $('.cb-student-select').each(function () {
                    var sid = $(this).data('student-id').toString();
                    $(this).prop('checked', selectedStudents.has(sid));
                });
            }

            // โหลดค่าเริ่มต้นจาก checkbox ที่ถูกติ๊กอยู่แล้ว
            $('.cb-student-select:checked').each(function () {
                selectedStudents.add($(this).data('student-id').toString());
            });

            // เมื่อมีการเปลี่ยนแปลง checkbox
            $(document).on('change', '.cb-student-select', function () {
                var sid = $(this).data('student-id').toString();
                if ($(this).is(':checked')) {
                    selectedStudents.add(sid);
                } else {
                    selectedStudents.delete(sid);
                }
            });

            // ก่อน submit form ให้สร้าง hidden input สำหรับคนที่ถูกเลือกทั้งหมด
            $('form').on('submit', function () {
                $('input[name="student_id_select[]"][type="hidden"]').remove();
                selectedStudents.forEach(function (sid) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'student_id_select[]',
                        value: sid
                    }).appendTo('form');
                });
            });

            if (typeof $.fn.DataTable !== 'undefined') {
                var table = $('#studentTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    paging: false,
                    language: {
                        search: "ค้นหา:",
                        lengthMenu: "แสดง _MENU_ รายการ",
                        info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                        infoEmpty: "ไม่มีข้อมูล",
                        infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                        zeroRecords: "ไม่พบผู้เรียนที่ตรงกับคำค้นหา",
                        paginate: {
                            first: "หน้าแรก",
                            last: "หน้าสุดท้าย",
                            next: "ถัดไป",
                            previous: "ก่อนหน้า"
                        }
                    },
                    columnDefs: [
                        { targets: [0, 3, 4], className: 'dt-center' }
                    ]
                });

                // Optional: Keep the old search input working with DataTables
                $('#searchStudent').on('keyup', function () {
                    table.search(this.value).draw();
                });

                $('#clearSearch').on('click', function () {
                    $('#searchStudent').val('');
                    table.search('').draw();
                });

                // ซ่อน/แสดง checkbox ตาม state ที่เก็บไว้ทุกครั้งที่ DataTables วาดใหม่
                table.on('draw', function () {
                    syncCheckboxState();
                });
            }
        });
    </script>
</body>

</html>
<?php
}
?>