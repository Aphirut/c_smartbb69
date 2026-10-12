<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";

    $del_id = $_REQUEST['del_id'] ?? null;  // Use null coalescing operator and remove mysqli::real_escape_string
    $act_id = $_REQUEST['act_id'] ?? null; // Use null coalescing operator and remove mysqli::real_escape_string

    if ($del_id !== null && is_numeric($del_id)) {
        try {
            // Delete from procurement table
            $sql = "DELETE FROM procurement WHERE procurement_id = :del_id AND school_id = :school_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['del_id' => $del_id, 'school_id' => $_SESSION['ss_school_id']]);

            // Delete from material table
            $sql = "DELETE FROM material WHERE procurement_id = :del_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['del_id' => $del_id]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            // Handle error as needed
            exit;
        }
    }
     echo "<meta http-equiv='refresh' content='0;url=procurement_show.php?act_id=$act_id'>";
}
?>