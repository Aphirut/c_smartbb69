<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit; // Added exit to prevent further execution
} else {
    include "./includes/connect.php";
    $del_id = isset($_REQUEST['del_id']) ? $_REQUEST['del_id'] : null;

    if ($del_id !== null && is_numeric($del_id)) {
		try {
            $stmt = $pdo->prepare("DELETE FROM reporting WHERE activity_id = :del_id AND school_id = :school_id LIMIT 1");
            $stmt->execute(['del_id' => $del_id, 'school_id' => $_SESSION['ss_school_id']]);
        } catch (PDOException $e) {
            echo "Error deleting record: " . $e->getMessage();
			exit; // Exit after displaying error message
        }
	}
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=reporting_main.php'>";
	exit; // Added exit after redirect
}
?>