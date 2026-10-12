<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit; // Added exit to prevent further execution
} else {
?>
    <!DOCTYPE html>
    <html>
    <head>
    <?php
        include "./modules/head.inc.php";
        include "./includes/connect.php";

        try {
            $stmt = $pdo->prepare("SELECT * FROM school WHERE school_id = :school_id LIMIT 1");
            $stmt->execute(['school_id' => $_SESSION['ss_school_id']]);
            $row = $stmt->fetch();

            if ($row) {
                $school_id = $_SESSION['ss_school_id'];
                $password = $row['password'];
                $school_name = $row['school_name'];
                $teacher_name = $row['teacher_name'];
                $teacher_position_id = $row['teacher_position_id'];
                $president_name = $row['president_name'];
                $president_position_id = $row['president_position_id'];
                $headprocurementer = $row['headprocurementer'];
                $procurementer = $row['procurementer'];
                $planner = $row['planner'];
                $continueter = $row['continueter'];
            $registrar = $row['registrar'] ?? '';
                $financial = $row['financial'];
                $address = $row['address'];
                $district = $row['district'];
                $amphur = $row['amphur'];
                $province = $row['province'];
                $postcode = $row['postcode'];
                $tel_teacher = $row['tel_teacher'];
                $phone = $row['phone'];
                $fax = $row['fax'];
                $latitude = $row['latitude'];
                $longtitude = $row['longtitude'];
                $photo = $row['photo'];
                $numbook_amp = $row['numbook_amp'];
                $status = $row['status'];
            } else {
                // Handle the case where no school data is found, perhaps redirect to an error page or display a message
                echo "No school data found for this ID.";
                exit;
            }
        } catch (PDOException $e) {
            echo "Error fetching school data: " . $e->getMessage();
            exit;
        }
    ?>
        <script>
            window.onload = function() {
                ShowSchoolType();
            }
        </script>
    </head>
    <body>
        <?php include "./modules/header.inc.php"; ?>
        <div class="wrapper">
            <header class="main-header">
                <a href="index.php" class="logo">
                    <span class="logo-mini"><b>Menu</b></span>
                    <span class="logo-lg">การศึกษาต่อเนื่อง</span>
                </a>
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <?php include "./modules/topblock.inc.php"; ?>
                </nav>
            </header>
            
            <aside class="main-sidebar">
                <?php include "./sidebar.php"; ?>
            </aside>

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>แก้ไขข้อมูลหน่วยงาน/สถานศึกษา</h1>
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">แก้ไขข้อมูลหน่วยงาน/สถานศึกษา</li>
                    </ol>
                </section>

                <section class="content">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="box">
                                <div class="box-body">
                                    <form class="form-horizontal" action="school_save.php" method="POST" enctype="multipart/form-data">
                                        <div class="box-body bg-success">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">รหัสสถานศึกษา</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $school_id; ?>" class="form-control" name="school_id" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ชื่อสถานศึกษา</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $school_name; ?>" class="form-control" name="school_name" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group" id="numbook_amp_hide">
                                                <label class="col-sm-4 control-label">เลขที่หนังสือ</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $numbook_amp; ?>" class="form-control" name="numbook_amp" autocomplete="off" placeholder="ศธ 0210.2209/">
                                                </div>
                                            </div>

                                            <div class="form-group" id="teacher_name_show">
                                                <label class="col-sm-4 control-label">ชื่อ-สกุล ครู</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $teacher_name; ?>" class="form-control" name="teacher_name" autocomplete="off" placeholder="นายอนัตชัย เกตุแก้ว">
                                                </div>
                                            </div>

                                            <div class="form-group" id="teacher_position_show">
                                                <label class="col-sm-4 control-label">ตำแหน่ง</label>
                                                <div class="col-sm-4">
                                                    <select class="form-control" id="teacher_position_id" name="teacher_position_id" style="width:100%">
                                                        <option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
                                                        <?php
                                                        try {
                                                            $stmt = $pdo->prepare("SELECT * FROM position WHERE type != '1' ORDER BY position_id ASC");
                                                            $stmt->execute();
                                                            while ($row = $stmt->fetch()) {
                                                                if ($row['position_id'] == $teacher_position_id) {
                                                                    echo "<option value='{$row['position_id']}' selected=''>{$row['position_name']}</option>";
                                                                } else {
                                                                    echo "<option value='{$row['position_id']}'>{$row['position_name']}</option>";
                                                                }
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo "Error fetching teacher positions: " . $e->getMessage();
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group" id="president_name_hide">
                                                <label class="col-sm-4 control-label">ชื่อ-สกุล ผู้บริหาร</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $president_name; ?>" class="form-control" name="president_name" autocomplete="off" placeholder="นายอนัตชัย เกตุแก้ว">
                                                </div>
                                            </div>

                                            <div class="form-group" id="president_position_hide">
                                                <label class="col-sm-4 control-label">ตำแหน่ง</label>
                                                <div class="col-sm-4">
                                                    <select class="form-control" id="president_position_id" name="president_position_id" style="width:100%">
                                                        <option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
                                                        <?php
                                                        try {
                                                            $stmt = $pdo->prepare("SELECT * FROM position WHERE type = '1' ORDER BY position_id ASC");
                                                            $stmt->execute();
                                                            while ($row = $stmt->fetch()) {
                                                                if ($row['position_id'] == $president_position_id) {
                                                                    echo "<option value='{$row['position_id']}' selected=''>{$row['position_name']}</option>";
                                                                } else {
                                                                    echo "<option value='{$row['position_id']}'>{$row['position_name']}</option>";
                                                                }
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo "Error fetching president positions: " . $e->getMessage();
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

											<div class="form-group" id="president_name_hide">
												<label class="col-sm-4 control-label">ชื่อ-สกุล หัวหน้าเจ้าหน้าที่พัสดุ</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $headprocurementer; ?>" class="form-control" name="headprocurementer" autocomplete="off" placeholder="นายยศวัจน์  รัตนนิกรเจริญ">
												</div>
											</div>
										
											<div class="form-group" id="president_name_hide">
												<label class="col-sm-4 control-label">ชื่อ-สกุล เจ้าหน้าที่พัสดุ</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $procurementer; ?>" class="form-control" name="procurementer" autocomplete="off" placeholder="นายยศวัจน์  รัตนนิกรเจริญ">
												</div>
											</div>

											<div class="form-group" id="president_name_hide">
												<label class="col-sm-4 control-label">ชื่อ-สกุล เจ้าหน้าที่งานแผน</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $planner; ?>" class="form-control" name="planner" autocomplete="off" placeholder="นายยศวัจน์  รัตนนิกรเจริญ">
												</div>
											</div>

											<div class="form-group" id="president_name_hide">
												<label class="col-sm-4 control-label">ชื่อ-สกุล เจ้าหน้าที่งานต่อเนื่อง</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $continueter; ?>" class="form-control" name="continueter" autocomplete="off" placeholder="นายยศวัจน์  รัตนนิกรเจริญ">
												</div>
											</div>

<div class="form-group">
												<label class="col-sm-4 control-label">ชื่อ-สกุล นายทะเบียน</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $registrar; ?>" class="form-control" name="registrar" autocomplete="off" placeholder="">
												</div>
											</div>

											<div class="form-group" id="president_name_hide">
												<label class="col-sm-4 control-label">ชื่อ-สกุล เจ้าหน้าที่การเงิน</label>
												<div class="col-sm-4">
													<input type="text" value="<?php echo $financial; ?>" class="form-control" name="financial" autocomplete="off" placeholder="นายยศวัจน์  รัตนนิกรเจริญ">
												</div>
											</div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ที่อยู่</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $address; ?>" class="form-control" name="address" required="required" autocomplete="off" placeholder="46 ม.6 ถ.เฉลิมพระเกียรติ ร.9">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ตำบล</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $district; ?>" class="form-control" name="district" required="required" autocomplete="off" placeholder="พลับพลา">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">อำเภอ</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $amphur; ?>" class="form-control" name="amphur" required="required" autocomplete="off" placeholder="เมืองจันทบุรี">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">จังหวัด</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $province; ?>" class="form-control" name="province" required="required" autocomplete="off" placeholder="จันทบุรี">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">รหัสไปรษณีย์</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $postcode; ?>" class="form-control" id="postcode" name="postcode" required="required" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group" id="tel_show">
                                                <label class="col-sm-4 control-label">โทรศัพท์ ครู</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $tel_teacher; ?>" class="form-control" id="tel_teacher" name="tel_teacher" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group" id="tel_hide">
                                                <label class="col-sm-4 control-label">โทรศัพท์ กศน.อำเภอ</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $phone; ?>" class="form-control" id="phone" name="phone" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group" id="fax_hide">
                                                <label class="col-sm-4 control-label">โทรสาร กศน.อำเภอ</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $fax; ?>" class="form-control" id="fax" name="fax" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ละติจูด</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $latitude; ?>" class="form-control" id="latitude" name="latitude" required="required" autocomplete="off" maxlength="11">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ลองติจูด</label>
                                                <div class="col-sm-4">
                                                    <input type="text" value="<?php echo $longtitude; ?>" class="form-control" id="longtitude" name="longtitude" required="required" autocomplete="off" maxlength="11">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label"><?php if($status == "3"){echo "รูปภาพครู";} else{echo "รูปภาพผู้บริหาร";}?></label>
                                                <div class="col-sm-4">
                                                    <input type="file" class="form-control" id="fileUpload" name="fileUpload" accept=".jpeg, .jpg, .png, .gif, .bmp" value="<?php echo $photo; ?>">
                                                    <?php
                                                    if ($photo != "") {
                                                        echo "<center><img src = ./pictures/school/$photo?" . time() . " width='50%'></center>";
                                                    }
                                                    else {
                                                        echo "<center><img src = ./pictures/noimage.jpg width='50%'></center>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                        <div class="box-footer" style="text-align: center">
                                            <button type="submit" class="btn btn-info">บันทึก</button>
                                            <button type="cancel" class="btn btn-default" onclick="window.location='index.php';return false;">ยกเลิก</button>
                                            <input type="hidden" id="school_status" value="<?php echo $status; ?>">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include "./modules/footer.inc.php"; ?>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                new Cleave('#postcode', {
                    numericOnly: true,
                    blocks: [5]
                });
                new Cleave('#tel_teacher', {
                    numericOnly: true,
                    delimiter: '-',
                    blocks: [3, 7]
                });
                new Cleave('#phone', {
                    numericOnly: true,
                    delimiter: '-',
                    blocks: [3, 7]
                });
                new Cleave('#fax', {
                    numericOnly: true,
                    delimiter: '-',
                    blocks: [3, 7]
                });
                new Cleave('#latitude', {
                    numeral: true,
                    numeralPositiveOnly: true,
                    numeralDecimalScale: 7,
                    numeralThousandsGroupStyle: 'none'
                });
                new Cleave('#longtitude', {
                    numeral: true,
                    numeralPositiveOnly: true,
                    numeralDecimalScale: 7,
                    numeralThousandsGroupStyle: 'none'
                });
            });
        </script>
        
        <script type="text/javascript">
            $(document).ready(function() {
                $('#teacher_position_id, #president_position_id').select2({
                    placeholder: '-- โปรดเลือก --',
                    minimumResultsForSearch: Infinity
                });
            });
        </script>

        <script type="text/javascript"> 
            $("#fileUpload").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("ต้องเป็นไฟล์ JPEG, JPG, PNG, GIF เท่านั้น");
                    document.getElementById("fileUpload").value = null; 
                }
                if (this.files[0].size/1024 > 1024) {
                    alert("ไฟล์ต้องขนาดไม่เกิน 1 mb");
                    document.getElementById("fileUpload").value = null; 
                }
            });
        </script>

        <script type="text/javascript">
            function ShowSchoolType() {
                if ($("#school_status").val() != "") {
                    if ($("#school_status").val() == 3) {
                        $("div[id$='_hide']").each(function(){
                            $(this).hide();
                        });
                    } else {
                        $("div[id$='_show']").each(function(){
                            $(this).hide();
                        });
                    }
                }
            }
        </script>
    </body>
    </html>
<?php
}
?>