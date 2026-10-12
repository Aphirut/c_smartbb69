<?php
include "./includes/connect.php";
include "./includes/function.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['skill_id'])) {
    $skill_id = $_POST['skill_id'];

    try {
        if (!isset($pdo)) {
            throw new Exception("Database connection not established");
        }

        $sql = "SELECT 
                    a.activity_name,
                    a.date_start,
                    a.date_end,
                    a.date_range,
                    a.place,
                    a.address,
                    a.men_study + a.women_study AS total_participants,
                    s.skill_id,
                    s.skill_name, 
                    a.school_id,
                    a.date_id,
                    sc.school_name
                FROM activity a
                INNER JOIN skill s ON a.skill_id = s.skill_id
                INNER JOIN school sc ON a.school_id = sc.school_id
                WHERE a.skill_id = :skill_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':skill_id', $skill_id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
       

        foreach ($results as &$row) {
            // แปลง skill_id เป็นชื่อประเภท
            switch ($row['skill_id']) {
                case 1:
                    $row['skill_id'] = 'New Skill';
                    break;
                case 2:
                    $row['skill_id'] = 'Upskill';
                    break;
                case 3:
                    $row['skill_id'] = 'Reskill';
                    break;
            }

            // จัดการวันที่
            $date = '';

            if ($row['date_id'] == "1") {
                if ($row['date_start'] && $row['date_end']) {
                    if($row['date_start'] == $row['date_end']){
                         $date = ADYearToBEYearTH(trim($row['date_start']), 'full');
                    } else {
                      $date = ADYearToBEYearTH(trim($row['date_start']), 'full') . " ถึง " . ADYearToBEYearTH(trim($row['date_end']), 'full');
                    }
                 
                } 
            } else if ($row['date_id'] == "2") {
                 if(isset($row['date_range']) && !empty($row['date_range'])){
                    $date = DateRangeToString(trim($row['date_range']),'full');
                 }
            }

            $row['date_range_display'] = $date;
            
            // ลบฟิลด์ที่ไม่ต้องการส่งกลับ
            unset($row['date_start']);
            unset($row['date_end']);
            unset($row['date_range']);
             unset($row['date_id']);
        }
        unset($row);


        header('Content-Type: application/json');
        echo json_encode($results);

    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error fetching data: ' . $e->getMessage()]);
        error_log("PHP Error: " . $e->getMessage());
    }
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
}

// ฟังก์ชันสำหรับแปลงเดือนเป็นเดือนไทยแบบย่อ
function getShortThaiMonth($month) {
    $thai_months = [
        1 => 'ม.ค.',
        2 => 'ก.พ.',
        3 => 'มี.ค.',
        4 => 'เม.ย.',
        5 => 'พ.ค.',
        6 => 'มิ.ย.',
        7 => 'ก.ค.',
        8 => 'ส.ค.',
        9 => 'ก.ย.',
        10 => 'ต.ค.',
        11 => 'พ.ย.',
        12 => 'ธ.ค.',
    ];
    return $thai_months[$month];
}
?>