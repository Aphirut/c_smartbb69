<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?php
        include "./modules/head.inc.php";
        include "./includes/connect.php";
        include "./includes/function.php";
        
        ?>
         <style>
           .table-header-custom {
            background-color: #cce5ff; /* A soft blue header */
        }
        .table-body-custom {
            background-color: #f0f8ff; /* A light blue table body*/
        }
         .table-custom th, .table-custom td {
          text-align: center; /* Center content in table cells */
         vertical-align: middle; /* Vertically center content */
        }
        .btn-custom {
           margin-right: 5px;
           margin-bottom: 3px; /* Add margin below the button */
        }
         .btn-group-custom {
          display: flex;
          flex-direction: column; /* Arrange buttons vertically */
          align-items: center; /* Center buttons horizontally */
           margin-bottom: 5px;
        }
       </style>
    </head>
    <?php include "./modules/header.inc.php"; ?>
    <body>
	<?php include "./modules/header.inc.php"; ?>
        <div class="wrapper">
            <header class="main-header">
                 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="index.php">
                            <span class="logo-mini d-block d-lg-none"><b>Menu</b></span>
                            <span class="logo-lg d-none d-lg-block">การศึกษาเพื่อพัฒนาตนเอง</span>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNavDropdown">
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item">
                                    <a href="#" class="nav-link sidebar-toggle" data-bs-toggle="offcanvas" role="button" aria-label="Toggle navigation">
                                        <span class="sr-only">Toggle navigation</span>
                                        <i class="fas fa-bars"></i>
                                    </a>
                                </li>
                            </ul>
                            <?php include "./modules/topblock.inc.php"; ?>
                        </div>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar bg-light">
				<?php include "./sidebar.php"; ?>
			</aside>


             <div class="content-wrapper">
                <section class="content-header mt-3">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>เบิกเงินวิทยากร</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">เอกสารลงเวลา</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header table-header-custom">
                                        <h3 class="card-title">Request for speaker fee payment</h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table id="example1" class="table table-striped table-custom" style="width:100%;">
                                            <thead class="table-header-custom">
                                                <tr>
                                                    <th  width="20px">ลำดับ</th>
                                                    <th  width="160px">วันที่</th>
                                                    <th >ชื่อ-โครงการ</th>
                                                    <th  width="200px">ใบปะหน้า</th>
                                                    <th  width="200px">ใบลงเวลาวิทยากร</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-body-custom">
                                                <?php
                                                $a = 0;
                                                try {
                                                    $sql = "SELECT activity.activity_id, activity.activity_name,
                                                                activity.date_id, activity.date_range,
                                                                activity.date_start, activity.date_end,
                                                                activity.format_id
                                                            FROM activity
                                                            WHERE activity.school_id = :school_id ORDER BY activity_id DESC";
                                                    $stmt = $pdo->prepare($sql);
                                                    $stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
                                                    $stmt->execute();

                                                    while ($row = $stmt->fetch()) {
                                                        $a++;
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
                                                        echo "<td >$a</td>";
                                                        echo "<td >$date</td> ";
                                                        echo "<td align='left'>{$row['activity_name']}</td> ";
                                                        echo "<td >
                                                            <div class='btn-group-custom'>";
                                                        echo "<a class='btn btn-sm btn-outline-primary btn-custom' href='phpword/cover_letter_word.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ใบปะหน้าส่งจังหวัด</a>   ";
                                                        echo "<a class='btn btn-sm btn-outline-primary btn-custom'  href='phpword/cover_view.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ใบปะหน้าพัฒนาตนเอง</a>   ";
                                                        echo "<a class='btn btn-sm btn-outline-primary btn-custom' href='phpword/cover_lecturer_view.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ใบปะหน้าค่าตอบแทนวิทยากร</a>   ";
                                                         echo "<a class='btn btn-sm btn-outline-danger btn-custom' href='phpword/payment_voucher.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> งบหน้าใบสำคัญคู่จ่าย</a>   ";
                                                          echo "<a class='btn btn-sm btn-outline-danger btn-custom' href='phpword/request_lecturer_payment.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ขออนุมัติจ่ายเงินค่าตอบแทนวิทยากร</a>   ";
                                                       echo "</div>
                                                        </td >";
                                                        echo "<td >
                                                            <div class='btn-group-custom'>";
                                                       echo "<a class='btn btn-sm btn-outline-primary btn-custom' href='reports/time_lec.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ใบลงเวลาวิทยากร</a>   ";
                                                        echo "<a class='btn btn-sm btn-outline-danger btn-custom' href='reports/time_lec_blank.php?act_id={$row['activity_id']}' target='_blank'><i class='fa fa-print' ></i> ใบลงเวลาวิทยากรเปล่า</a>   ";
                                                        echo "</div>
                                                        </td></tr>";
                                                    }
                                                } catch (PDOException $e) {
                                                    echo "Error fetching activities: " . $e->getMessage();
                                                    error_log("PDO Exception: " . $e->getMessage());
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
                </section>
            </div>
            <?php include "./modules/footer.inc.php"; ?>
        </div>

           <script>
             $(document).ready(function() {
                $('#example1').DataTable({
                    responsive: true,
                    "language": {
                        "search": "ค้นหา",
                        "lengthMenu": " แสดงผล _MENU_ ต่อหน้า",
                        "info": "หน้า _PAGE_ จาก _PAGES_ หน้า",
                        "zeroRecords": "ไม่มีข้อมูล",
                        "infoEmpty": "ไม่พบผลการค้นหา",
                        "infoFiltered": "(จากทั้งหมด _MAX_ รายการ)"
                    }
                });
             });
          </script>
    </body>
    </html>
    <?php
}
?>