<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "c-smartbb68";

error_reporting(0);

try {
    $dsn = "mysql:host={$hostname};dbname={$dbname};charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // ให้ PDO โยน Exception เมื่อมีข้อผิดพลาด
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // กำหนดให้ Fetch ข้อมูลเป็น Associative Array โดยค่าเริ่มต้น
        PDO::ATTR_EMULATE_PREPARES   => false,                 // ปิดการจำลอง Prepared Statements
    ];
    $pdo = new PDO($dsn, $username, $password, $options);

    // ถ้าเชื่อมต่อสำเร็จ จะมาถึงตรงนี้
    // echo "เชื่อมต่อฐานข้อมูลสำเร็จ"; // เอาออกหรือแสดงข้อความอื่นตามต้องการ

} catch (PDOException $e) {
    // ดักจับ Exception ที่เกิดจากการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
    echo "เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage();
    exit;
}

// ตัวแปร $pdo พร้อมใช้งานแล้วสำหรับการ query ฐานข้อมูล
?>
<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
?>
    <!DOCTYPE html>
    <html>
	<head>
		<?php
		include "./modules/head.inc.php";
		// include "./includes/connect.php"; // Removed old connection
		include "./includes/function.php";
		$edit_id = isset($_REQUEST['edit_id']) ? $_REQUEST['edit_id'] : ''; // Added isset check

		if ($edit_id != "") {
			try {
				$stmt = $pdo->prepare("SELECT * FROM activity WHERE activity_id = :edit_id AND school_id = :school_id LIMIT 1");
				$stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
				$stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
				$stmt->execute();
				$row = $stmt->fetch();

				if ($row) {
					$activity_id = $row['activity_id'];
					$course_id = $row['course_id'];
					$format_id = $row['format_id'];
					$attribute_id = $row['attribute_id'];
					$type_id = $row['type_id'];

					$activity_name = $row['activity_name'];
					$objective = $row['objective'];
					$date_id = $row['date_id'];
					$lecturer_id = $row['lecturer_id'];
					$place = $row['place'];
					$address = $row['address'];
					$latitude = $row['latitude'];
					$longtitude = $row['longtitude'];

					$date_start = ADYearToBEYear($row['date_start']);
					$date_end = ADYearToBEYear($row['date_end']);
					$date_range = $row['date_range'];
					$time_start = $row['time_start'];
					$time_end = $row['time_end'];

					$year_id = $row['year_id'];
					$plan_id = $row['plan_id'];
					$lecturer_cost = $row['lecturer_cost'];
					$material_cost = $row['material_cost'];
					$board_cost = $row['board_cost'];
					$food_cost = $row['food_cost'];
					$drink_cost = $row['drink_cost'];
					$place_cost = $row['place_cost'];
					$car_cost = $row['car_cost'];
					$other_cost = $row['other_cost'];
					$total_costs = $row['total_costs'];
				} else {
					echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
				}
			} catch (PDOException $e) {
				echo "Error fetching activity: " . $e->getMessage();
				// Consider logging the error or displaying a user-friendly message
			}
		}
		?>
		<script>
			window.onload = function() {
				ShowActivity();
			}
		</script>
	</head>
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
				<section class="content-header">
					<h1>
						<?php if($activity_id == ""){echo "เพิ่มกลุ่ม/โครงการ";} else{echo "แก้ไขกลุ่ม/โครงการ";}?>
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="act_main.php">กิจกรรม/โครงการทั้งหมด</a></li>
						<li class="active"><?php if($activity_id == ""){echo "เพิ่มกลุ่ม/โครงการ";} else{echo "แก้ไขกลุ่ม/โครงการ";}?></li>
					</ol>
				</section>

				<form action="act_save.php<?php if($activity_id != ""){echo "?submit=EDIT";}?>" method="post" name="form1" id="form1">
					<section class="content">
						<div class="box box-default">
							<div class="box-header with-border">
								<h3 class="box-title">หลักสูตร/โครงการ</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>

							<div class="box-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group col-sm-12">
											<label>หลักสูตร/โครงการ</label>
											<select class="form-control" id="course_id" name="course_id" style="width:100%" required="required">
												<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
												<?php
												try {
													$stmt = $pdo->query("SELECT * FROM course ORDER BY course_name ASC, course_hour ASC");
													$a = 0;
													while ($row = $stmt->fetch()) {
														$a++;
														if (isset($course_id) && $row['course_id'] == $course_id) {
															echo "<option value='" . $row['course_id'] . "' selected=''>" . $a . "." . $row['course_name'] . " " . $row['course_hour'] . " ชม.</option>";
														} else {
															echo "<option value='" . $row['course_id'] . "'>" . $a . "." . $row['course_name'] . " " . $row['course_hour'] . " ชม.</option>";
														}
													}
												} catch (PDOException $e) {
													echo "Error fetching courses: " . $e->getMessage();
												}
												?>
											</select>
										</div>

										<div class="form-group col-sm-12">
											<label>รูปแบบการจัด</label>
											<select class="form-control" id="format_id" name="format_id" style="width:100%" required="required">
												<!-- จำกัดเหลือเพียงตัวเลือก "ฝึกอบรมประชาชน" เท่านั้น -->
												<option value="1" selected>ฝึกอบรมประชาชน</option>
											</select>
										</div>

									<div class="col-md-6">
 											<div class="form-group col-sm-12">
												<label>ลักษณะการจัด</label>
											<select class="form-control" id="attribute_id" name="attribute_id" style="width:100%" required="required">
												<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
												<?php
												try {
													$stmt = $pdo->query("SELECT * FROM attribute ORDER BY attribute_id ASC");
													while ($row = $stmt->fetch()) {
														if (isset($attribute_id) && $row['attribute_id'] == $attribute_id) {
															echo "<option value='" . $row['attribute_id'] . "' selected=''>" . $row['attribute_name'] . "</option>";
														} else {
															echo "<option value='" . $row['attribute_id'] . "'>" . $row['attribute_name'] . "</option>";
														}
													}
												} catch (PDOException $e) {
													echo "Error fetching attributes: " . $e->getMessage();
												}
												?>
											</select>
										</div>

										<div class="form-group col-sm-12">
											<label>ประเภทกิจกรรม</label>
											<select class="form-control" id="type_id" name="type_id" style="width:100%" required="required">
												<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
												<!-- จำกัดเหลือเพียง 4 ประเภทตามที่ต้องการ (โครงการพัฒนาอาชีพชุมชน, การพัฒนาทักษะชีวิต, การพัฒนาสังคมและชุมชน, ศาสตร์พระราชาหลักปรัชญาเศรษฐกิจพอเพียง) -->
												<option value="1" <?php echo (isset($type_id) && $type_id == 1) ? 'selected=""' : ''; ?>>โครงการพัฒนาอาชีพชุมชน</option>
												<option value="2" <?php echo (isset($type_id) && $type_id == 2) ? 'selected=""' : ''; ?>>การพัฒนาทักษะชีวิต</option>
												<option value="3" <?php echo (isset($type_id) && $type_id == 3) ? 'selected=""' : ''; ?>>การพัฒนาสังคมและชุมชน</option>
												<option value="4" <?php echo (isset($type_id) && $type_id == 4) ? 'selected=""' : ''; ?>>ศาสตร์พระราชาหลักปรัชญาเศรษฐกิจพอเพียง</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="box box-danger">
									<div class="box-header">
										<h3 class="box-title"><i class="fa fa-flag-o text-olive"></i> โครงการ</h3>
									</div>
									<div class="box-body">
										<div class="form-group col-sm-12">
											<label>ชื่อโครงการ :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-archive"></i>
                                                </div>
												<input type="text" class="form-control" name="activity_name" required="required" autocomplete="off" placeholder="โครงการฝึกทักษะอาชีพตัดผมชาย" value="<?php echo isset($activity_name) ? $activity_name : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>วัถตุประสงค์ :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-crosshairs"></i>
                                                </div>
												<input type="text" class="form-control" name="objective" required="required" autocomplete="off" placeholder="เพื่อให้ความรู้ในการประกอบอาชีพตัดผมชาย" value="<?php echo isset($objective) ? $objective : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>รูปแบบวันที่ :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
												<select class="form-control" id="date_id" name="date_id" style="width:100%" required="required">
													<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
													<?php
													try {
														$stmt = $pdo->query("SELECT * FROM date ORDER BY date_id ASC");
														while ($row = $stmt->fetch()) {
															if (isset($date_id) && $row['date_id'] == $date_id) {
																echo "<option value='" . $row['date_id'] . "' selected=''>" . $row['date_name'] . "</option>";
															} else {
																echo "<option value='" . $row['date_id'] . "'>" . $row['date_name'] . "</option>";
															}
														}
													} catch (PDOException $e) {
														echo "Error fetching dates: " . $e->getMessage();
													}
													?>
												</select>
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>วิทยากร :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-folder"></i>
                                                </div>
												<select class="form-control" id="lecturer_id" name="lecturer_id" style="width:100%" required="required">
													<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
													<?php
													try {
														$stmt = $pdo->prepare("SELECT * FROM lecturer WHERE school_id = :school_id ORDER BY lecturer_id DESC");
														$stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
														$stmt->execute();
														while ($row = $stmt->fetch()) {
															if (isset($lecturer_id) && $row['lecturer_id'] == $lecturer_id) {
																echo "<option value='" . $row['lecturer_id'] . "' selected=''>" . $row['firstname'] . "  " . $row['lastname'] . " | " . $row['talent'] . " </option>";
															} else {
																echo "<option value='" . $row['lecturer_id'] . "'>" . $row['firstname'] . " " . $row['lastname'] . " | " . $row['talent'] . " </option>";
															}
														}
													} catch (PDOException $e) {
														echo "Error fetching lecturers: " . $e->getMessage();
													}
													?>
												</select>
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>สถานที่จัด :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
												<input type="text" class="form-control" name="place" required="required" autocomplete="off" placeholder="ห้องประชุมอาคาร... (เฉพาะสถานที่)" value="<?php echo isset($place) ? $place : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>ที่อยู่ :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-map"></i>
                                                </div>
												<input type="text" class="form-control" name="address" required="required" placeholder="283 ถนนศุขประยูร ตำบลหน้าเมือง อำเภอเมือง จังหวัดฉะเชิงเทรา 24000 (เฉพาะที่อยู่)" value="<?php echo isset($address) ? $address : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>ละติจูด :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </div>
												<input type="text" class="form-control" id="latitude" name="latitude" required="required" autocomplete="off" maxlength="10" value="<?php echo isset($latitude) ? $latitude : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12">
											<label>ลองติจูด :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-globe"></i>
                                                </div>
												<input type="text" class="form-control" id="longtitude" name="longtitude" required="required" autocomplete="off" maxlength="11" value="<?php echo isset($longtitude) ? $longtitude : ''; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="box box-primary">
									<div class="box-header">
										<h3 class="box-title"><i class="fa fa-clock-o text-olive"></i> วันที่ดำเนินการ</h3>
									</div>
									<div class="box-body">
										<div class="form-group col-sm-6" id="date_start_show">
											<label>วันที่เริ่มจัด :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
												<input type="text" class="form-control" id="date_start" name="date_start" autocomplete="off" value="<?php echo isset($date_start) ? $date_start : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-6" id="date_end_show">
											<label>วันที่สิ้นสุด :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
												<input type="text" class="form-control" id="date_end" name="date_end" autocomplete="off" value="<?php echo isset($date_end) ? $date_end : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-12" id="date_range_show">
											<label>วันที่จัดกิจกรรม :</label>
											<div class="input-group">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
												<input type="text" class="form-control" id="date_range" name="date_range" autocomplete="off" readonly="readonly" value="<?php echo isset($date_range) ? $date_range : ''; ?>">
											</div>
										</div>

										<div class="form-group col-sm-6">
											<label>เวลาเริ่มจัด :</label>
											<div class="input-group">
												<input type="text" class="form-control pull-right" id="time_start" name="time_start" autocomplete="off" required="required" onkeypress="return false;" value="<?php echo isset($time_start) ? $time_start : ''; ?>">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-6">
											<label>เวลาสิ้นสุด :</label>
											<div class="input-group">
												<input type="text" class="form-control pull-right" id="time_end" name="time_end" autocomplete="off" required="required" value="<?php echo isset($time_end) ? $time_end : ''; ?>">
												<div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="box box-info">
									<div class="box-header">
										<h3 class="box-title"><i class="fa fa-money text-olive"></i> ขอใช้งบประมาณ</h3>
									</div>

									<div class="box-body">
										<div class="form-group col-sm-12">
											<label>ปีงบประมาณ :</label>
											<select class="form-control" id="year_id" name="year_id" style="width:100%" required="required">
												<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
												<?php
												try {
													$stmt = $pdo->query("SELECT * FROM year ORDER BY year_id ASC");
													$a = 0;
													while ($row = $stmt->fetch()) {
														$a++;
														if (isset($year_id) && $row['year_id'] == $year_id) {
															echo "<option value='" . $row['year_id'] . "' selected=''>" . $a . ".ปีงบประมาณ " . $row['year_name'] . "</option>";

														} else {
															echo "<option value='" . $row['year_id'] . "'>" . $a . ".ปีงบประมาณ " . $row['year_name'] . "</option>";
														}
													}
												} catch (PDOException $e) {
													echo "Error fetching years: " . $e->getMessage();
												}
												?>
											</select>
										</div>

										<div class="form-group col-sm-12">
											<label>ค่าใช้จ่ายกิจกรรม :</label>
											<select class="form-control" id="plan_id" name="plan_id" style="width:100%" required="required">
												<option value="" selected="selected" disabled="true">-- โปรดเลือก --</option>
												<?php
												try {
													$stmt = $pdo->query("SELECT * FROM plan ORDER BY plan_id ASC");
													$a = 0;
													while ($row = $stmt->fetch()) {
														$a++;
														if (isset($plan_id) && $row['plan_id'] == $plan_id) {
															echo "<option value='" . $row['plan_id'] . "' selected=''>" . $a . "." . $row['plan_name_show'] . "</option>";

														} else {
															echo "<option value='" . $row['plan_id'] . "'>" . $a . "." . $row['plan_name_show'] . "</option>";
														}
													}
												} catch (PDOException $e) {
													echo "Error fetching plans: " . $e->getMessage();
												}
												?>
											</select>
										</div>

										<div class="form-group col-sm-4">
											<label>ค่าวิทยากร :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="lecturer_cost" name="lecturer_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($lecturer_cost) ? $lecturer_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4">
											<label>ค่าวัสดุ :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="material_cost" name="material_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($material_cost) ? $material_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="board_cost_show">
											<label>ค่าป้าย :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="board_cost" name="board_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($board_cost) ? $board_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="food_cost_show">
											<label>ค่าอาหาร :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="food_cost" name="food_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($food_cost) ? $food_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="drink_cost_show">
											<label>ค่าอาหารว่าง :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="drink_cost" name="drink_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($drink_cost) ? $drink_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="place_cost_show">
											<label>ค่าสถานที่ :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="place_cost" name="place_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($place_cost) ? $place_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="car_cost_show">
											<label>ค่าจ้างเหมารถ :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="car_cost" name="car_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($car_cost) ? $car_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4" id="other_cost_show">
											<label>ค่าใช้จ่ายอื่น :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="other_cost" name="other_cost" autocomplete="off" maxlength="10" required="required" value="<?php echo isset($other_cost) ? $other_cost : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="form-group col-sm-4">
											<label>รวมทั้งสิ้น :</label>
											<div class="input-group">
												<input type="text" class="form-control" id="total_costs" name="total_costs" autocomplete="off" required="required" readonly value="<?php echo isset($total_costs) ? $total_costs : ''; ?>">
												<div class="input-group-addon">
													บาท
                                                </div>
											</div>
										</div>

										<div class="box-footer" style="text-align: center">
											<button type="submit" class="btn btn-info" onclick="CheckDateRange(date_id.value)">บันทึก</button>
											<button type="cancel" class="btn btn-default" onclick="window.location='act_main.php';return false;">ยกเลิก</button>
											<input type="hidden" name="activity_id" value="<?php echo isset($activity_id) ? $activity_id : ''; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				</form>
			</div>
			<?php include "./modules/footer.inc.php"; ?>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {
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
				new Cleave('#date_start', {
					date: true,
					delimiter: '/',
					datePattern: ['d', 'm', 'Y']
				});
				new Cleave('#date_end', {
					date: true,
					delimiter: '/',
					datePattern: ['d', 'm', 'Y']
				});
				document.querySelectorAll("input[id$='_cost']").forEach(function(ex) {
					new Cleave(ex, {
						numeral: true,
						numeralPositiveOnly: true,
						numeralThousandsGroupStyle: 'thousand'
					});
				});
				new Cleave('#total_costs', {
					numeral: true,
					numeralPositiveOnly: true,
					numeralThousandsGroupStyle: 'thousand'
				});
			});
		</script>

		<script type="text/javascript">
			$.datepicker.setDefaults({
				showButtonPanel: true,
				regional: 'th',
				changeMonth: true,
				changeYear: true,
				yearOffSet: 543,
				dateFormat: 'dd/mm/yy',
				dayNames: ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
				dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
				monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
				monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
				yearRange: '-100:+1'
			});

			$(document).ready(function() {
				$('#course_id').select2({
					placeholder: '-- โปรดเลือก --',
				});
				$('#format_id, #attribute_id, #type_id, #date_id, #lecturer_id, #year_id, #plan_id').select2({
					placeholder: '-- โปรดเลือก --',
					minimumResultsForSearch: Infinity
				});
				$('#date_start, #date_end').datepicker();
				$('#date_range').multiDatesPicker();
				$('#time_start, #time_end').timepicki();
			});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#format_id').change(function() {
					$("div[id$='_cost_show']").each(function() {
						if ($("#format_id").val() == 1) {
							$(this).show();
						} else {
							$(this).hide();
						}
					});
					$("div[id$='_cost_show'] input").each(function() {
						if ($("#format_id").val() == 1) {
							this.value = "";
						} else {
							this.value = "0";
						}
					});
					sumTotalCost();
				});

				$('#date_id').change(function() {
					if ($("#date_id").val() == 1) {
						$("#date_range_show").hide();
						$("#date_start_show, #date_end_show").show();
					} else {
						$("#date_start_show, #date_end_show").hide();
						$("#date_range_show").show();
					}
					$('#date_range').multiDatesPicker('resetDates', 'picked');
					$('#date_start, #date_end').datepicker('setDate', null);
				});

				$("input[id$='_cost'").on('input', function(){
					sumTotalCost();
				});
			});
		</script>

		<script type="text/javascript">
			function ShowActivity(){
				if ($("#format_id").val() == 2 || $("#format_id").val() == 3) {
					$("div[id$='_cost_show']").each(function() {
						$(this).hide();
					});
				}
				if ($("#date_id").val() == 1) {
					$("#date_range_show").hide();
				}
				if ($("#date_id").val() == 2) {
					$("#date_start_show, #date_end_show").hide();
				}
			}
		</script>

		<script type="text/javascript"> 
			function sumTotalCost() {
				var total = 0;
				$("input[id$='_cost']").each(function(i, el) {
					total += Number(el.value.replace(/,/g, ""));
				});
				total = total.toFixed(2);
				total = total.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				$('#total_costs').val(total);
			}
		</script>

		<script type="text/javascript">
			function CheckDateRange(type){
				if ((type == 2 && $('#date_range').val() == "") || (type == 1 && $('#date_start').val() == "") || (type == 1 && $('#date_end').val() == "")) {
					alert('กรุณากรอกข้อมูลให้ครบถ้วน');
					event.preventDefault();
				}
			}
		</script>
	</body>
	</html>
<?php
} 
?>