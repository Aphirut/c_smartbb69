<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "../includes/connect.php";

    $lecturer_id = isset($_POST["lecturer_id"]) ? trim($_POST["lecturer_id"]) : '';
    $idcard = isset($_POST["idcard"]) ? str_replace('-', '', trim($_POST["idcard"])) : '';
    $school_id = $_SESSION['ss_school_id'];

    $check = false;
    if ($idcard != "" && $school_id != "") {
        try {
            $stmt = $pdo->prepare("SELECT lecturer_id FROM lecturer WHERE school_id = :school_id AND idcard = :idcard LIMIT 1");
            $stmt->execute(['school_id' => $school_id, 'idcard' => $idcard]);
            $row = $stmt->fetch();

            if ($lecturer_id == "") {
                if (!$row) {
                    $check = true;
                }
            } else {
                if (!$row || ($lecturer_id == $row['lecturer_id'])) {
                    $check = true;
                }
            }
        } catch (PDOException $e) {
            $check = false;
        }
    }
    echo json_encode($check);
}
?>