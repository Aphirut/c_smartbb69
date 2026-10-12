<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit();
}

include "./includes/connect.php";
include "./includes/function.php";

function show_error_and_redirect(string $message, string $redirect_url): void {
    $_SESSION['error_message'] = $message;
    echo '<div style="color: red; margin-bottom: 10px;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
    echo "<meta http-equiv=\"refresh\" content=\"5;URL=$redirect_url\" />";
    unset($_SESSION['error_message']);
    exit();
}

/**
 * ประกอบที่อยู่เต็มจากส่วนที่แยกไว้ (ใช้กับคอลัมน์ activity.address เดิม)
 * ใช้ addr_format_part() จาก includes/function.php เพื่อไม่ให้คำนำหน้าซ้อนกัน
 */
function compose_activity_address(string $addr_no, string $village, string $street, string $tambon, string $amphur, string $province, string $postcode): string {
    $parts = [];

    if (trim($addr_no) !== '')  $parts[] = trim($addr_no);
    if (trim($village) !== '')  $parts[] = trim($village);
    if (trim($street) !== '')   $parts[] = addr_format_part($street, 'ถนน', ['ถนน', 'ซอย', 'ตรอก'], ['ซอย', 'ตรอก']);
    if (trim($tambon) !== '')   $parts[] = addr_format_part($tambon, 'ตำบล', ['ตำบล', 'แขวง', 'ต.'], ['แขวง']);
    if (trim($amphur) !== '')   $parts[] = addr_format_part($amphur, 'อำเภอ', ['อำเภอ', 'เขต', 'อ.'], ['เขต']);
    if (trim($province) !== '') $parts[] = addr_format_part($province, 'จังหวัด', ['จังหวัด', 'จ.'], ['กรุงเทพมหานคร']);
    if (trim($postcode) !== '') $parts[] = trim($postcode);

    // activity.address เป็น varchar(250)
    return mb_substr(implode(' ', $parts), 0, 250);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submit = $_GET['submit'] ?? '';
    $activity_id = $_POST['activity_id'] ?? null;
    $school_id = $_SESSION['ss_school_id'] ?? null;

    $activity_data = [];
    if ($submit === 'EDIT' && $activity_id !== null) {
        try {
            $sql_select = "SELECT * FROM activity WHERE activity_id = :activity_id AND school_id = :school_id";
            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute(['activity_id' => $activity_id, 'school_id' => $school_id]);
            $activity_data = $stmt_select->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            show_error_and_redirect("Error fetching activity data: " . $e->getMessage(), 'act_main.php');
        }
    }

    $course_id = $_POST['course_id'] ?? ($activity_data['course_id'] ?? '');
    $format_id = $_POST['format_id'] ?? ($activity_data['format_id'] ?? '');
    $attribute_id = $_POST['attribute_id'] ?? ($activity_data['attribute_id'] ?? '');
    $type_id = $_POST['type_id'] ?? ($activity_data['type_id'] ?? '');
    $activity_name = $_POST['activity_name'] ?? ($activity_data['activity_name'] ?? '');
    $objective = $_POST['objective'] ?? ($activity_data['objective'] ?? '');
    $lecturer_id = $_POST['lecturer_id'] ?? ($activity_data['lecturer_id'] ?? '');
    $place = $_POST['place'] ?? ($activity_data['place'] ?? '');

    // ===== ที่อยู่ (แยกส่วน: บ้านเลขที่ / หมู่บ้าน / ถนน / ตำบล / อำเภอ / จังหวัด / รหัสไปรษณีย์) =====
    if (isset($_POST['province']) || isset($_POST['addr_no'])) {
        $addr_no  = trim($_POST['addr_no'] ?? '');
        $village  = trim($_POST['village'] ?? '');
        $street   = trim($_POST['street'] ?? '');
        $tambon   = trim($_POST['tambon'] ?? '');
        $amphur   = trim($_POST['amphur'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $postcode = trim($_POST['postcode'] ?? '');

        $composed = compose_activity_address($addr_no, $village, $street, $tambon, $amphur, $province, $postcode);
        // ถ้าไม่ได้กรอกอะไรเลย ให้คงที่อยู่เดิมไว้
        $address = ($composed !== '') ? $composed : ($activity_data['address'] ?? '');
    } else {
        // ฟอร์มอื่นที่ยังส่งที่อยู่มาเป็นข้อความเดียว
        $address  = trim($_POST['address'] ?? ($activity_data['address'] ?? ''));
        $addr_no  = $activity_data['addr_no'] ?? '';
        $village  = $activity_data['village'] ?? '';
        $street   = $activity_data['street'] ?? '';
        $tambon   = $activity_data['tambon'] ?? '';
        $amphur   = $activity_data['amphur'] ?? '';
        $province = $activity_data['province'] ?? '';
        $postcode = $activity_data['postcode'] ?? '';
    }

    $latitude = $_POST['latitude'] ?? ($activity_data['latitude'] ?? '');
    $longtitude = $_POST['longtitude'] ?? ($activity_data['longtitude'] ?? '');
    $date_id = $_POST['date_id'] ?? ($activity_data['date_id'] ?? '');
    $date_start = $_POST['date_start'] ?? ($activity_data['date_start'] ?? '');
    $date_end = $_POST['date_end'] ?? ($activity_data['date_end'] ?? '');
    $date_range = $_POST['date_range'] ?? ($activity_data['date_range'] ?? '');
    $time_start = $_POST['time_start'] ?? ($activity_data['time_start'] ?? '');
    $time_end = $_POST['time_end'] ?? ($activity_data['time_end'] ?? '');
    $year_id = $_POST['year_id'] ?? ($activity_data['year_id'] ?? '');
    $plan_id = $_POST['plan_id'] ?? ($activity_data['plan_id'] ?? '');
    $lecturer_cost = $_POST['lecturer_cost'] ?? ($activity_data['lecturer_cost'] ?? '0');
    $material_cost = $_POST['material_cost'] ?? ($activity_data['material_cost'] ?? '0');
    $board_cost = $_POST['board_cost'] ?? ($activity_data['board_cost'] ?? '0');
    $food_cost = $_POST['food_cost'] ?? ($activity_data['food_cost'] ?? '0');
    $drink_cost = $_POST['drink_cost'] ?? ($activity_data['drink_cost'] ?? '0');
    $place_cost = $_POST['place_cost'] ?? ($activity_data['place_cost'] ?? '0');
    $car_cost = $_POST['car_cost'] ?? ($activity_data['car_cost'] ?? '0');
    $other_cost = $_POST['other_cost'] ?? ($activity_data['other_cost'] ?? '0');
    $total_costs = $_POST['total_costs'] ?? ($activity_data['total_costs'] ?? '0');
    $total_graduate = $_POST['total_graduate'] ?? ($activity_data['total_graduate'] ?? '0');
    $total_study = $_POST['total_study'] ?? ($activity_data['total_study'] ?? '');
    $skill_id = $_POST['skill_id'] ?? ($activity_data['skill_id'] ?? '');

    $status_line = isset($_POST['status_line']) && is_numeric($_POST['status_line'])
                   ? intval($_POST['status_line'])
                   : '';

    $status_assess = $_POST['status_assess'] ?? ($activity_data['status_assess'] ?? '');
    $men_study = $_POST['men_study'] ?? ($activity_data['men_study'] ?? '');
    $women_study = $_POST['women_study'] ?? ($activity_data['women_study'] ?? '');

    // ===== รับค่าผู้ยืมเงิน =====
    $borrower_id = trim($_POST['borrower_id'] ?? ($activity_data['borrower_id'] ?? ''));

    // ===== รับค่า checkbox เครดิต (ถ้าไม่ติ๊กจะไม่ส่งมา = 0) =====
    $lecturer_cost_credit = isset($_POST['lecturer_cost_credit']) ? 1 : 0;
    $material_cost_credit = isset($_POST['material_cost_credit']) ? 1 : 0;
    $board_cost_credit    = isset($_POST['board_cost_credit'])    ? 1 : 0;
    $food_cost_credit     = isset($_POST['food_cost_credit'])     ? 1 : 0;
    $drink_cost_credit    = isset($_POST['drink_cost_credit'])    ? 1 : 0;
    $place_cost_credit    = isset($_POST['place_cost_credit'])    ? 1 : 0;
    $car_cost_credit      = isset($_POST['car_cost_credit'])      ? 1 : 0;
    $other_cost_credit    = isset($_POST['other_cost_credit'])    ? 1 : 0;

    // ===== 🆕 รับค่า ระดับหลักสูตร (พื้นฐาน / ชั้นกลาง / ชั้นสูง) =====
    $level_basic        = isset($_POST['level_basic'])        ? 1 : 0;
    $level_intermediate = isset($_POST['level_intermediate']) ? 1 : 0;
    $level_advanced     = isset($_POST['level_advanced'])     ? 1 : 0;

    $level_basic_rate        = $level_basic
        ? floatval(preg_replace('/[^0-9.]/', '', $_POST['level_basic_rate'] ?? '0'))
        : 0;
    $level_intermediate_rate = $level_intermediate
        ? floatval(preg_replace('/[^0-9.]/', '', $_POST['level_intermediate_rate'] ?? '0'))
        : 0;
    $level_advanced_rate     = $level_advanced
        ? floatval(preg_replace('/[^0-9.]/', '', $_POST['level_advanced_rate'] ?? '0'))
        : 0;

    $lecturer_cost = floatval(preg_replace('/[^0-9.]/', '', $lecturer_cost));
    $material_cost = floatval(preg_replace('/[^0-9.]/', '', $material_cost));
    $board_cost = floatval(preg_replace('/[^0-9.]/', '', $board_cost));
    $food_cost = floatval(preg_replace('/[^0-9.]/', '', $food_cost));
    $drink_cost = floatval(preg_replace('/[^0-9.]/', '', $drink_cost));
    $place_cost = floatval(preg_replace('/[^0-9.]/', '', $place_cost));
    $car_cost = floatval(preg_replace('/[^0-9.]/', '', $car_cost));
    $other_cost = floatval(preg_replace('/[^0-9.]/', '', $other_cost));
    $total_costs = floatval(preg_replace('/[^0-9.]/', '', $total_costs));
    $total_graduate = intval($total_graduate);
    $total_study = intval($total_study);
    $men_study = intval($men_study);
    $women_study = intval($women_study);
    $status_assess = intval($status_assess);

    if ($date_id == 1) {
        $date_start = !empty($date_start) ? date('Y-m-d', strtotime($date_start)) : null;
        $date_end = !empty($date_end) ? date('Y-m-d', strtotime($date_end)) : null;
        $date_range = null;
    } elseif ($date_id == 2) {
        $date_start = null;
        $date_end = null;
    } else {
        $date_start = null;
        $date_end = null;
        $date_range = null;
    }

    try {
        if ($submit === 'EDIT' && $activity_id !== null) {
            $sql = "UPDATE activity SET
                school_id = :school_id,
                course_id = :course_id,
                format_id = :format_id,
                attribute_id = :attribute_id,
                type_id = :type_id,
                activity_name = :activity_name,
                objective = :objective,
                lecturer_id = :lecturer_id,
                place = :place,
                address = :address,
                addr_no = :addr_no,
                village = :village,
                street = :street,
                tambon = :tambon,
                amphur = :amphur,
                province = :province,
                postcode = :postcode,
                latitude = :latitude,
                longtitude = :longtitude,
                date_id = :date_id,
                date_start = :date_start,
                date_end = :date_end,
                date_range = :date_range,
                time_start = :time_start,
                time_end = :time_end,
                year_id = :year_id,
                plan_id = :plan_id,
                lecturer_cost = :lecturer_cost,
                material_cost = :material_cost,
                board_cost = :board_cost,
                food_cost = :food_cost,
                drink_cost = :drink_cost,
                place_cost = :place_cost,
                car_cost = :car_cost,
                other_cost = :other_cost,
                total_costs = :total_costs,
                total_graduate = :total_graduate,
                total_study = :total_study,
                status_line = :status_line,
                status_assess = :status_assess,
                men_study = :men_study,
                women_study = :women_study,
                skill_id = :skill_id,
                borrower_id = :borrower_id,
                lecturer_cost_credit = :lecturer_cost_credit,
                material_cost_credit = :material_cost_credit,
                board_cost_credit = :board_cost_credit,
                food_cost_credit = :food_cost_credit,
                drink_cost_credit = :drink_cost_credit,
                place_cost_credit = :place_cost_credit,
                car_cost_credit = :car_cost_credit,
                other_cost_credit = :other_cost_credit,
                level_basic = :level_basic,
                level_basic_rate = :level_basic_rate,
                level_intermediate = :level_intermediate,
                level_intermediate_rate = :level_intermediate_rate,
                level_advanced = :level_advanced,
                level_advanced_rate = :level_advanced_rate
            WHERE activity_id = :activity_id AND school_id = :school_id_check";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'activity_id' => $activity_id,
                'school_id' => $school_id,
                'school_id_check' => $school_id,
                'course_id' => $course_id,
                'format_id' => $format_id,
                'attribute_id' => $attribute_id,
                'type_id' => $type_id,
                'activity_name' => $activity_name,
                'objective' => $objective,
                'lecturer_id' => $lecturer_id,
                'place' => $place,
                'address' => $address,
                'addr_no' => $addr_no,
                'village' => $village,
                'street' => $street,
                'tambon' => $tambon,
                'amphur' => $amphur,
                'province' => $province,
                'postcode' => $postcode,
                'latitude' => $latitude,
                'longtitude' => $longtitude,
                'date_id' => $date_id,
                'date_start' => $date_start,
                'date_end' => $date_end,
                'date_range' => $date_range,
                'time_start' => $time_start,
                'time_end' => $time_end,
                'year_id' => $year_id,
                'plan_id' => $plan_id,
                'lecturer_cost' => $lecturer_cost,
                'material_cost' => $material_cost,
                'board_cost' => $board_cost,
                'food_cost' => $food_cost,
                'drink_cost' => $drink_cost,
                'place_cost' => $place_cost,
                'car_cost' => $car_cost,
                'other_cost' => $other_cost,
                'total_costs' => $total_costs,
                'total_graduate' => $total_graduate,
                'total_study' => $total_study,
                'status_line' => $status_line,
                'status_assess' => $status_assess,
                'men_study' => $men_study,
                'women_study' => $women_study,
                'skill_id' => $skill_id,
                'borrower_id' => $borrower_id,
                'lecturer_cost_credit' => $lecturer_cost_credit,
                'material_cost_credit' => $material_cost_credit,
                'board_cost_credit' => $board_cost_credit,
                'food_cost_credit' => $food_cost_credit,
                'drink_cost_credit' => $drink_cost_credit,
                'place_cost_credit' => $place_cost_credit,
                'car_cost_credit' => $car_cost_credit,
                'other_cost_credit' => $other_cost_credit,
                'level_basic' => $level_basic,
                'level_basic_rate' => $level_basic_rate,
                'level_intermediate' => $level_intermediate,
                'level_intermediate_rate' => $level_intermediate_rate,
                'level_advanced' => $level_advanced,
                'level_advanced_rate' => $level_advanced_rate
            ]);
        } else {
            $sql = "INSERT INTO activity (
                school_id,
                course_id,
                format_id,
                attribute_id,
                type_id,
                activity_name,
                objective,
                lecturer_id,
                place,
                address,
                addr_no,
                village,
                street,
                tambon,
                amphur,
                province,
                postcode,
                latitude,
                longtitude,
                date_id,
                date_start,
                date_end,
                date_range,
                time_start,
                time_end,
                year_id,
                plan_id,
                lecturer_cost,
                material_cost,
                board_cost,
                food_cost,
                drink_cost,
                place_cost,
                car_cost,
                other_cost,
                total_costs,
                total_graduate,
                total_study,
                status_line,
                status_assess,
                men_study,
                women_study,
                skill_id,
                borrower_id,
                lecturer_cost_credit,
                material_cost_credit,
                board_cost_credit,
                food_cost_credit,
                drink_cost_credit,
                place_cost_credit,
                car_cost_credit,
                other_cost_credit,
                level_basic,
                level_basic_rate,
                level_intermediate,
                level_intermediate_rate,
                level_advanced,
                level_advanced_rate
            ) VALUES (
                :school_id,
                :course_id,
                :format_id,
                :attribute_id,
                :type_id,
                :activity_name,
                :objective,
                :lecturer_id,
                :place,
                :address,
                :addr_no,
                :village,
                :street,
                :tambon,
                :amphur,
                :province,
                :postcode,
                :latitude,
                :longtitude,
                :date_id,
                :date_start,
                :date_end,
                :date_range,
                :time_start,
                :time_end,
                :year_id,
                :plan_id,
                :lecturer_cost,
                :material_cost,
                :board_cost,
                :food_cost,
                :drink_cost,
                :place_cost,
                :car_cost,
                :other_cost,
                :total_costs,
                :total_graduate,
                :total_study,
                :status_line,
                :status_assess,
                :men_study,
                :women_study,
                :skill_id,
                :borrower_id,
                :lecturer_cost_credit,
                :material_cost_credit,
                :board_cost_credit,
                :food_cost_credit,
                :drink_cost_credit,
                :place_cost_credit,
                :car_cost_credit,
                :other_cost_credit,
                :level_basic,
                :level_basic_rate,
                :level_intermediate,
                :level_intermediate_rate,
                :level_advanced,
                :level_advanced_rate
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'school_id' => $school_id,
                'course_id' => $course_id,
                'format_id' => $format_id,
                'attribute_id' => $attribute_id,
                'type_id' => $type_id,
                'activity_name' => $activity_name,
                'objective' => $objective,
                'lecturer_id' => $lecturer_id,
                'place' => $place,
                'address' => $address,
                'addr_no' => $addr_no,
                'village' => $village,
                'street' => $street,
                'tambon' => $tambon,
                'amphur' => $amphur,
                'province' => $province,
                'postcode' => $postcode,
                'latitude' => $latitude,
                'longtitude' => $longtitude,
                'date_id' => $date_id,
                'date_start' => $date_start,
                'date_end' => $date_end,
                'date_range' => $date_range,
                'time_start' => $time_start,
                'time_end' => $time_end,
                'year_id' => $year_id,
                'plan_id' => $plan_id,
                'lecturer_cost' => $lecturer_cost,
                'material_cost' => $material_cost,
                'board_cost' => $board_cost,
                'food_cost' => $food_cost,
                'drink_cost' => $drink_cost,
                'place_cost' => $place_cost,
                'car_cost' => $car_cost,
                'other_cost' => $other_cost,
                'total_costs' => $total_costs,
                'total_graduate' => $total_graduate,
                'total_study' => $total_study,
                'status_line' => $status_line,
                'status_assess' => $status_assess,
                'men_study' => $men_study,
                'women_study' => $women_study,
                'skill_id' => $skill_id,
                'borrower_id' => $borrower_id,
                'lecturer_cost_credit' => $lecturer_cost_credit,
                'material_cost_credit' => $material_cost_credit,
                'board_cost_credit' => $board_cost_credit,
                'food_cost_credit' => $food_cost_credit,
                'drink_cost_credit' => $drink_cost_credit,
                'place_cost_credit' => $place_cost_credit,
                'car_cost_credit' => $car_cost_credit,
                'other_cost_credit' => $other_cost_credit,
                'level_basic' => $level_basic,
                'level_basic_rate' => $level_basic_rate,
                'level_intermediate' => $level_intermediate,
                'level_intermediate_rate' => $level_intermediate_rate,
                'level_advanced' => $level_advanced,
                'level_advanced_rate' => $level_advanced_rate
            ]);
        }

        $_SESSION['success_message'] = "บันทึกข้อมูลเรียบร้อย";
        echo "<script>window.location='act_main.php';</script>";
        exit();
    } catch (PDOException $e) {
        show_error_and_redirect("Error during data manipulation: " . $e->getMessage(), 'act_main.php');
    }
}
?>