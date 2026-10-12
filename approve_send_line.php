<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_name("C_Smart");
session_start();

function redirectWithDelay($url, $delaySeconds) {
    echo '<script type="text/javascript">';
    echo "setTimeout(function() { window.location.href = '$url'; }, " . ($delaySeconds * 1000) . ");";
    echo '</script>';
}


if (!isset($_SESSION['ss_school_id']) || $_SESSION['ss_login_status'] != 2) {
    include 'error_page.php';
    redirectWithDelay('index.php', 10); 
    exit;
} else {
    include "./includes/connect.php";
    include "./includes/function.php";
    include "./includes/config.inc.php";

    $id_school_ch = substr($_SESSION['ss_school_id'], 0, 6);
    $act_id = $_GET['act_id'] ?? null;

    if (empty($act_id)) {
        echo "Invalid activity ID.";
        redirectWithDelay('approve_main.php', 10);
        exit;
    }

    try {
        $sql = "SELECT course.course_name, type.type_name, format.format_name, lecturer.firstname, lecturer.lastname, activity.place, activity.address, activity.date_id, activity.date_range, activity.date_start, activity.date_end, activity.time_start, activity.time_end, activity.status_line FROM activity INNER JOIN course ON activity.course_id = course.course_id INNER JOIN format ON activity.format_id = format.format_id INNER JOIN lecturer ON activity.lecturer_id = lecturer.lecturer_id INNER JOIN type ON activity.type_id = type.type_id WHERE activity.activity_id = :act_id AND activity.school_id LIKE :id_school_ch";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':act_id' => $act_id, ':id_school_ch' => $id_school_ch . '%']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (empty($row['status_line'])) {
                if ($row['date_id'] == "1") {
                    if ($row['date_start'] == $row['date_end']) {
                        $date = ADYearToBEYearTH($row['date_start'], 1);
                    } else {
                        $date = ADYearToBEYearTH($row['date_start'], 1) . " ถึง " . ADYearToBEYearTH($row['date_end'], 1);
                    }
                } else {
                    $date = DateRangeToString($row['date_range'], 1);
                }

                $date_line = ADYearToBEYearTH(date("Y-m-d"), 1);
                $time_line = date("H:i");

                $msg_line = "*แจ้งเปิดหลักสูตรการศึกษาต่อเนื่อง*\n"
                    . "หลักสูตร : {$row['course_name']}\n"
                    . "ประเภท : {$row['type_name']}\n"
                    . "รูปแบบ : {$row['format_name']}\n"
                    . "วิทยากร : {$row['firstname']} {$row['lastname']}\n"
                    . "จัดที่ : {$row['place']} {$row['address']}\n"
                    . "วันที่ : $date\n"
                    . "เวลา : {$row['time_start']} น. - {$row['time_end']} น.\n"
                    . "รายละเอียด : $_Config_site/act_show_line.php?act_id=$act_id\n\n"
                    . "@ {$_SESSION['ss_school_name']} @\n"
                    . "$date_line $time_line น.";

                    // ส่งข้อมูลไปยัง webhook.php ด้วย cURL
                    $webhookUrl = 'http://c-smart.click/c_smartbb68/webhook.php'; // แทนที่ด้วย URL ของ webhook.php ของคุณ
                    $data = [
                        'message' => $msg_line,
                        'act_id' => $act_id // ส่ง act_id ไปด้วยเพื่ออัพเดท status_line
                    ];

                    $ch = curl_init($webhookUrl);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // ส่งข้อมูลแบบ POST
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    // ตรวจสอบผลลัพธ์จาก webhook.php
                     if ($response === "OK") { // webhook.php ควร return "OK" เมื่อสำเร็จ
                        echo "ส่งคำขอไปยัง Webhook สำเร็จ";
                        redirectWithDelay('approve_main.php', 3); // เพิ่มดีเลย์ 3 วินาที
                    } else {
                        echo "เกิดข้อผิดพลาดในการส่งคำขอไปยัง Webhook: " . $response;
                        redirectWithDelay('approve_main.php', 5); // เพิ่มดีเลย์ 5 วินาที
                    }

            }
        } else {
            echo "No activity found with ID: $act_id";
            redirectWithDelay('approve_main.php', 10);
            exit;
        }

       // redirectWithDelay('approve_main.php', 0); // ไม่ delay เมื่อสำเร็จ // ตัดส่วนนี้ออก เพราะมี redirect ในเงื่อนไขแล้ว

    } catch (PDOException $e) {
         echo "Database Error: " . $e->getMessage();
         redirectWithDelay('approve_main.php', 10);
    }
}
?>