<?php
include "./includes/connect.php"; // Include the database connection

error_reporting(0);

session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {

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
           echo "ไม่พบข้อมูลสถานศึกษา";
           exit;
        }

    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
        exit;
    }

?>
<!DOCTYPE html>
<html lang="th">
<head>
	<?php
		include "./modules/head.inc.php";
	?>
	<style>
		.school-edit-card .card-header {
			background-color: #0d6efd;
			color: #fff;
		}
		.school-edit-card .card-header .card-title {
			font-weight: 600;
		}
		.form-section-title {
			font-size: 0.95rem;
			font-weight: 600;
			color: #0d6efd;
			margin-top: 0.75rem;
			margin-bottom: 0.5rem;
			padding-bottom: 0.25rem;
			border-bottom: 1px solid #dee2e6;
		}
		.img-preview {
			max-width: 220px;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
	</style>
	<script>
		window.onload = function() {
			ShowSchoolType();
		}
	</script>
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
						<i class="fas fa-edit text-primary me-2"></i>
						แก้ไขข้อมูลหน่วยงาน/สถานศึกษา
					</h4>
				</div>
				<div class="col-auto">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb mb-0 small">
							<li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
							<li class="breadcrumb-item active">แก้ไขข้อมูลหน่วยงาน/สถานศึกษา</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Content -->
	<div class="app-content">
		<div class="container-fluid">
			<div class="card school-edit-card shadow-sm">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fas fa-school me-2"></i> ข้อมูลสถานศึกษา
					</h3>
				</div>
				<div class="card-body p-4">
					<form action="school_save.php" method="POST" enctype="multipart/form-data" id="schoolEditForm">
						<input type="hidden" id="school_status" value="<?php echo $status; ?>">

						<!-- ข้อมูลพื้นฐาน -->
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">รหัสสถานศึกษา</label>
								<input type="text" value="<?php echo htmlspecialchars($school_id); ?>" class="form-control" name="school_id" readonly>
							</div>
							<div class="col-md-6">
								<label class="form-label">ชื่อสถานศึกษา</label>
								<input type="text" value="<?php echo htmlspecialchars($school_name); ?>" class="form-control" name="school_name" readonly>
							</div>
						</div>

						<!-- เลขที่หนังสือ (เฉพาะบางประเภท) -->
						<div class="row g-3 mt-1" id="numbook_amp_hide">
							<div class="col-md-6">
								<label class="form-label">เลขที่หนังสือ</label>
								<input type="text" value="<?php echo htmlspecialchars($numbook_amp); ?>" class="form-control" name="numbook_amp" autocomplete="off" placeholder="ศธ 0210.2209/">
							</div>
						</div>

						<!-- ข้อมูลครู (แสดงเมื่อ status = 3) -->
						<div id="teacher_name_show">
							<div class="form-section-title mt-3">ข้อมูลครู</div>
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล ครู</label>
									<input type="text" value="<?php echo htmlspecialchars($teacher_name); ?>" class="form-control" name="teacher_name" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>
								<div class="col-md-6">
									<label class="form-label">ตำแหน่ง</label>
									<select class="form-select" id="teacher_position_id" name="teacher_position_id">
										<option value="" disabled>-- โปรดเลือก --</option>
										<?php
										try {
											$stmt = $pdo->query("SELECT * FROM position WHERE type != '1' ORDER BY position_id ASC");
											while ($row_position = $stmt->fetch()) {
												$selected = ($row_position['position_id'] == $teacher_position_id) ? 'selected' : '';
												echo "<option value='{$row_position['position_id']}' {$selected}>{$row_position['position_name']}</option>";
											}
										} catch (PDOException $e) {
											echo "<option value=''>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</option>";
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<!-- ข้อมูลผู้บริหาร (ซ่อนเมื่อ status = 3) -->
						<div id="president_name_hide">
							<div class="form-section-title mt-3">ข้อมูลผู้บริหาร</div>
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล ผู้บริหาร</label>
									<input type="text" value="<?php echo htmlspecialchars($president_name); ?>" class="form-control" name="president_name" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>
								<div class="col-md-6">
									<label class="form-label">ตำแหน่ง</label>
									<select class="form-select" id="president_position_id" name="president_position_id">
										<option value="" disabled>-- โปรดเลือก --</option>
										<?php
										try {
											$stmt = $pdo->query("SELECT * FROM position WHERE type = '1' ORDER BY position_id ASC");
											while ($row_position = $stmt->fetch()) {
												$selected = ($row_position['position_id'] == $president_position_id) ? 'selected' : '';
												echo "<option value='{$row_position['position_id']}' {$selected}>{$row_position['position_name']}</option>";
											}
										} catch (PDOException $e) {
											echo "<option value=''>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</option>";
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<!-- บุคลากรอื่น ๆ (ซ่อนเมื่อ status = 3) -->
						<div id="personnel_hide">
							<div class="form-section-title mt-3">บุคลากรที่เกี่ยวข้อง</div>
							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล หัวหน้าเจ้าหน้าที่พัสดุ</label>
									<input type="text" value="<?php echo htmlspecialchars($headprocurementer); ?>" class="form-control" name="headprocurementer" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>
								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล เจ้าหน้าที่พัสดุ</label>
									<input type="text" value="<?php echo htmlspecialchars($procurementer); ?>" class="form-control" name="procurementer" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>

								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล เจ้าหน้าที่งานแผน</label>
									<input type="text" value="<?php echo htmlspecialchars($planner); ?>" class="form-control" name="planner" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>
								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล เจ้าหน้าที่งานต่อเนื่อง</label>
									<input type="text" value="<?php echo htmlspecialchars($continueter); ?>" class="form-control" name="continueter" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>

<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล นายทะเบียน</label>
									<input type="text" value="<?php echo htmlspecialchars($registrar); ?>" class="form-control" name="registrar" autocomplete="off" placeholder="">
								</div>

								<div class="col-md-6">
									<label class="form-label">ชื่อ-สกุล เจ้าหน้าที่การเงิน</label>
									<input type="text" value="<?php echo htmlspecialchars($financial); ?>" class="form-control" name="financial" autocomplete="off" placeholder="นายณัชนันท์ วงศ์แสงธรรม">
								</div>
							</div>
						</div>

						<!-- ที่อยู่ -->
						<div class="form-section-title mt-3">ที่อยู่และติดต่อ</div>
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">ที่อยู่</label>
								<input type="text" value="<?php echo htmlspecialchars($address); ?>" class="form-control" name="address" required autocomplete="off" placeholder="188/39">
							</div>
							<div class="col-md-6">
								<label class="form-label">ตำบล</label>
								<input type="text" value="<?php echo htmlspecialchars($district); ?>" class="form-control" name="district" required autocomplete="off" placeholder="ในเมือง">
							</div>
							<div class="col-md-4">
								<label class="form-label">อำเภอ</label>
								<input type="text" value="<?php echo htmlspecialchars($amphur); ?>" class="form-control" name="amphur" required autocomplete="off" placeholder="เมือง">
							</div>
							<div class="col-md-4">
								<label class="form-label">จังหวัด</label>
								<input type="text" value="<?php echo htmlspecialchars($province); ?>" class="form-control" name="province" required autocomplete="off" placeholder="พิจิตร">
							</div>
							<div class="col-md-4">
								<label class="form-label">รหัสไปรษณีย์</label>
								<input type="text" value="<?php echo htmlspecialchars($postcode); ?>" class="form-control" id="postcode" name="postcode" required autocomplete="off">
							</div>
						</div>

						<!-- เบอร์โทร -->
						<div class="row g-3 mt-1" id="contact_phones">
							<div class="col-md-6" id="tel_show">
								<label class="form-label">โทรศัพท์ ครู</label>
								<input type="text" value="<?php echo htmlspecialchars($tel_teacher); ?>" class="form-control" id="tel_teacher" name="tel_teacher" autocomplete="off">
							</div>
							<div class="col-md-6 tel-hide-group" id="tel_hide">
								<label class="form-label">โทรศัพท์ กศน.อำเภอ</label>
								<input type="text" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" id="phone" name="phone" autocomplete="off">
							</div>
							<div class="col-md-6 tel-hide-group" id="fax_hide">
								<label class="form-label">โทรสาร กศน.อำเภอ</label>
								<input type="text" value="<?php echo htmlspecialchars($fax); ?>" class="form-control" id="fax" name="fax" autocomplete="off">
							</div>
						</div>

						<!-- พิกัด -->
						<div class="row g-3 mt-1">
							<div class="col-md-6">
								<label class="form-label">ละติจูด</label>
								<input type="text" value="<?php echo htmlspecialchars($latitude); ?>" class="form-control" id="latitude" name="latitude" required autocomplete="off" maxlength="11">
							</div>
							<div class="col-md-6">
								<label class="form-label">ลองติจูด</label>
								<input type="text" value="<?php echo htmlspecialchars($longtitude); ?>" class="form-control" id="longtitude" name="longtitude" required autocomplete="off" maxlength="11">
							</div>
						</div>

						<!-- รูปภาพ -->
						<div class="row g-3 mt-3">
							<div class="col-md-12">
								<label class="form-label">
									<?php echo ($status == "3") ? "รูปภาพครู" : "รูปภาพผู้บริหาร"; ?>
								</label>
								<div class="row align-items-center">
									<div class="col-md-5">
										<input type="file" class="form-control" id="fileUpload" name="fileUpload" accept=".jpeg,.jpg,.png,.gif,.bmp">
										<div class="form-text">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 1MB</div>
									</div>
									<div class="col-md-7 mt-2 mt-md-0">
										<?php if ($photo != ""): ?>
											<img src="./pictures/school/<?php echo htmlspecialchars($photo); ?>?t=<?php echo time(); ?>" class="img-preview" alt="รูปภาพปัจจุบัน">
										<?php else: ?>
											<img src="./pictures/noimage.jpg" class="img-preview" alt="ไม่มีรูปภาพ">
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>

						<!-- ปุ่มบันทึก -->
						<div class="d-flex justify-content-center gap-3 mt-4 pt-3 border-top">
							<button type="submit" class="btn btn-primary px-5">
								<i class="fas fa-save me-2"></i> บันทึก
							</button>
							<button type="button" class="btn btn-secondary px-5" onclick="window.location='index.php';return false;">
								<i class="fas fa-times me-2"></i> ยกเลิก
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php include "./modules/footer.inc.php"; ?>
</main>

</div><!-- /.app-wrapper -->

		<!-- Page Scripts -->
		<script>
			$(document).ready(function() {
				// Cleave formatting
				if (typeof Cleave !== "undefined") {
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
				}

				// Select2 for position dropdowns
				if (typeof $.fn.select2 !== "undefined") {
					$('#teacher_position_id, #president_position_id').select2({
						placeholder: '-- โปรดเลือก --',
						minimumResultsForSearch: Infinity,
						width: '100%'
					});
				}

				// File upload validation
				$("#fileUpload").on("change", function () {
					const fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
					const fileName = $(this).val().split('.').pop().toLowerCase();

					if ($.inArray(fileName, fileExtension) === -1) {
						alert("ต้องเป็นไฟล์ JPEG, JPG, PNG, GIF เท่านั้น");
						this.value = null;
						return;
					}
					if (this.files[0] && this.files[0].size / 1024 > 1024) {
						alert("ไฟล์ต้องขนาดไม่เกิน 1 MB");
						this.value = null;
					}
				});
			});

			// Show/hide fields based on school type (status)
			function ShowSchoolType() {
				const status = $("#school_status").val();
				if (status === "") return;

				if (status == 3) {
					// ครู (status=3) → ซ่อนฟิลด์ที่ลงท้ายด้วย _hide + tel/fax กลุ่ม
					$("div[id$='_hide']").hide();
					$(".tel-hide-group").hide();
					// แสดงฟิลด์ครู
					$("#teacher_name_show").show();
				} else {
					// อื่น ๆ → ซ่อนฟิลด์ที่ลงท้ายด้วย _show
					$("div[id$='_show']").hide();
					$(".tel-hide-group").show();
					// แสดงส่วนผู้บริหารและบุคลากร
					$("#president_name_hide, #personnel_hide").show();
				}
			}

			// Backup call in case window.onload fires too early
			$(document).ready(function() {
				if (typeof ShowSchoolType === "function") {
					ShowSchoolType();
				}
			});
		</script>
</body>
</html>
<?php
}
?>