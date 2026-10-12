<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit; // เพิ่ม exit เพื่อไม่ให้รันโค้ดส่วนที่เหลือ
} else {
    // Include PDO connection
    include "./includes/connect.php"; 
	include "./includes/function.php";
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
    <?php
        include "./modules/head.inc.php";
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
        }

       </style>
    <title>การประเมินผลฯ - ระบบการศึกษาต่อเนื่อง</title>
    </head>
	
    <body>
	<body>
	<?php include "./modules/header.inc.php"; ?>
        <div class="wrapper">
            <header class="main-header">
                 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="index.php">
                            <span class="logo-mini d-block d-lg-none"><b>Menu</b></span>
                            <span class="logo-lg d-none d-lg-block">การศึกษาต่อเนื่อง</span>
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
                                <h1>การประเมินผลฯ</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                                    <li class="breadcrumb-item active">การประเมินผลฯ</li>
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
                                        <h3 class="card-title">ประเมินกิจกรรม</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-custom" style="width:100%">
                                                <thead class="table-header-custom">
                                                    <tr>
                                                        <th  width="20px">ลำดับ</th>
                                                        <th  width="160px">วันที่</th>
                                                        <th >ชื่อ-โครงการ</th>
                                                        <th  width="120px">แจ้งจัด</th>
                                                        <th  width="120px">ทะเบียนรายชื่อ</th>
                                                        <th  width="120px">คำสั่งแต่งตั้ง</th>
                                                        <th  width="120px">เชิญวิทยากร</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-body-custom">
                                                    <?php
                                                    $a = 0;
                                                    $sql = "SELECT activity.activity_id, activity.activity_name, 
                                                            activity.date_id, activity.date_range, 
                                                            activity.date_start, activity.date_end, 
                                                            activity.format_id 
                                                            FROM activity 
                                                            WHERE activity.school_id = :school_id ORDER BY activity_id DESC";
                                                        
                                                    try {
                                                        $stmt = $pdo->prepare($sql);
                                                        $stmt->execute(['school_id' => $_SESSION['ss_school_id']]);

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
                                                            echo "<td>$a</td>";
                                                            echo "<td>$date</td> ";
                                                            echo "<td class='text-start'>{$row['activity_name']}</td> ";
                                                           echo "<td > ";
                                                           echo "<a class='btn btn-sm btn-outline-primary btn-custom' href='phpword/act_permission_word.php?act_id={$row['activity_id']}' target='_blank'><i class='fas fa-print' ></i> แจ้งจัด</a>   ";
                                                            echo "<td > ";
                                                            echo "<a class='btn btn-sm btn-outline-success btn-custom' href='phpword/act_student_list_word.php?act_id={$row['activity_id']}'><i class='fas fa-cog' ></i> ทะเบียนรายชื่อ</a>   ";
                                                            echo "</td>";
                                                             echo "<td > ";
                                                            echo "<a class='btn btn-sm btn-outline-success btn-custom' href='phpword/act_lecturer_command_word.php?act_id={$row['activity_id']}'><i class='fas fa-cog' ></i> คำสั่งแต่งตั้ง</a>   ";
                                                            echo "</td>";
                                                            echo "<td > ";
                                                            echo "<a class='btn btn-sm btn-outline-danger btn-custom' href='phpword/act_lecturer_invite_word.php?act_id={$row['activity_id']}' target='_blank'><i class='fas fa-print' ></i> เชิญวิทยากร</a>   ";
                                                            echo "</td></tr>";
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
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
            $('#example1').DataTable( {
                responsive: true,
                language: {
                    search: "ค้นหา",
                    lengthMenu: "แสดงผล _MENU_ ต่อหน้า",
                    info: "หน้า _PAGE_ จาก _PAGES_ หน้า",
                    zeroRecords: "ไม่มีข้อมูล",
                    infoEmpty: "ไม่พบผลการค้นหา",
                    infoFiltered: "(จากทั้งหมด _MAX_ รายการ)"
                }
             } );
         });
        </script>
    </body>
    </html>
<?php
} 
?>