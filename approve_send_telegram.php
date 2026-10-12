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

                $msg_line = "🔵 *แจ้งเปิดหลักสูตรการศึกษาต่อเนื่อง*\n"
                                    . "📚 หลักสูตร : {$row['course_name']}\n"
                                    . "🏷️ ประเภท : {$row['type_name']}\n"
                                    . "📝 รูปแบบ : {$row['format_name']}\n"
                                    . "🧑‍🏫 วิทยากร : {$row['firstname']} {$row['lastname']}\n"
                                    . "📍 จัดที่ : {$row['place']} {$row['address']}\n"
                                    . "📅 วันที่ : $date\n"
                                    . "⏱️ เวลา : {$row['time_start']} น. - {$row['time_end']} น.\n"
                                    . "🔗 รายละเอียด : $_Config_site/act_show_line.php?act_id=$act_id\n\n"
                                    . "@ {$_SESSION['ss_school_name']} @\n"
                                    . "$date_line $time_line น.";
                    
                // ปิดการส่ง Telegram ชั่วคราว (โทเค็นบอตเดิมใช้ไม่ได้ 401) -> อนุมัติในระบบอย่างเดียว; เปลี่ยนเป็น true เมื่อได้โทเค็นใหม่
                $sendTelegram = false;

                if (!$sendTelegram) {
                    $updateStmt = $pdo->prepare("UPDATE activity SET status_line = 1 WHERE activity_id = :act_id");
                    $updateStmt->execute([':act_id' => $act_id]);
                    echo "อนุมัติจัดตั้งกลุ่มสำเร็จ (ไม่ได้ส่งข้อความ Telegram)";
                    redirectWithDelay('approve_main.php', 2);
                    exit;
                }

                // --- Telegram Bot Integration ---
                $botToken = '7769868639:AAFe61J4uC60SvUBBIZwkamEbhdy5rrQ3vY'; // **ใส่ Token ของ Bot คุณที่นี่**
                $chatId = '-1002199108729';     // **ใส่ Chat ID ของกลุ่ม (ต้องมีเครื่องหมายลบนำหน้า)**

                $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

                $data = [
                  'chat_id' => $chatId,
                  'text' => $msg_line,
                ];
                
                $options = [
                    'http' => [
                      'method' => 'POST',
                      'header' => 'Content-type: application/x-www-form-urlencoded',
                      'content' => http_build_query($data)
                    ]
                  ];

                  $context = stream_context_create($options);
                  $result = file_get_contents($apiUrl, false, $context);
                  
                  if ($result === FALSE) {
                    echo "เกิดข้อผิดพลาดในการส่งข้อความไปยัง Telegram";
                    redirectWithDelay('approve_main.php', 5);
                  } else {
                    $response = json_decode($result, true);
                    if ($response['ok']) {

                        // อัปเดต status_line ในฐานข้อมูลเมื่อส่ง Telegram สำเร็จ
                        $updateSql = "UPDATE activity SET status_line = 1 WHERE activity_id = :act_id";
                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->execute([':act_id' => $act_id]);

                        echo "ส่งข้อความไปยัง Telegram กลุ่มสำเร็จ";
                        redirectWithDelay('approve_main.php', 3);

                    } else {
                        echo "เกิดข้อผิดพลาดในการส่งข้อความไปยัง Telegram กลุ่ม: " . $response['description'];
                        redirectWithDelay('approve_main.php', 5);
                    }
                  }
                // --- End Telegram Bot Integration ---
            }
        } else {
            echo "No activity found with ID: $act_id";
            redirectWithDelay('approve_main.php', 10);
            exit;
        }
    } catch (PDOException $e) {
         echo "Database Error: " . $e->getMessage();
         redirectWithDelay('approve_main.php', 10);
    }
}
?>