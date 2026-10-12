<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
    include "./includes/connect.php";
    include "./includes/function.php";

    $school_id = $_SESSION['ss_school_id'];
    $procurement_id = $_POST['procurement_id'] ?? null;
    $activity_id = $_POST['activity_id'] ?? $_POST['act_id'] ?? null;
    $payee_id = $_POST['payee_id'] ?? null;
    $expense_id = $_POST['expense_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $bill_number = trim($_POST['bill_number'] ?? '');
    $inspector_name = $_POST['inspector_name'] ?? '';
    $inspector_position_id = $_POST['inspector_position_id'] ?? null;
    $use_date = BEYearToADYear($_POST['use_date'] ?? '');
    $procurement_paid = str_replace(',', '', $_POST['procurement_paid'] ?? 0);

    if ($activity_id !== null && $payee_id !== null && $expense_id !== null) {
        try {
            if ($procurement_id == null) {
                // Insert new record
                $sql = "INSERT INTO procurement (
                            school_id, activity_id, payee_id, bill_number, expense_id, amount, inspector_name, inspector_position_id, use_date, procurement_paid
                            ) VALUES (
                            :school_id, :activity_id, :payee_id, :bill_number, :expense_id, :amount, 
                            :inspector_name, :inspector_position_id, :use_date, :procurement_paid)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'school_id' => $school_id,
                    'activity_id' => $activity_id,
                    'payee_id' => $payee_id,
                    'bill_number' => ($bill_number !== '' ? $bill_number : null),
                    'expense_id' => $expense_id,
                    'amount' => $amount,
                    'inspector_name' => $inspector_name,
                    'inspector_position_id' => $inspector_position_id,
                    'use_date' => $use_date,
                    'procurement_paid' => $procurement_paid
                ]);

               $procurement_id = $pdo->lastInsertId();


            } else {
                // Update existing record
                $sql = "UPDATE procurement SET 
                            activity_id = :activity_id, payee_id = :payee_id, bill_number = :bill_number,
                            expense_id = :expense_id, amount = :amount, 
                            inspector_name = :inspector_name, inspector_position_id = :inspector_position_id,
                            use_date = :use_date, procurement_paid = :procurement_paid 
                            WHERE procurement_id = :procurement_id AND school_id = :school_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'activity_id' => $activity_id,
                    'payee_id' => $payee_id,
                    'bill_number' => ($bill_number !== '' ? $bill_number : null),
                    'expense_id' => $expense_id,
                    'amount' => $amount,
                    'inspector_name' => $inspector_name,
                    'inspector_position_id' => $inspector_position_id,
                    'use_date' => $use_date,
                    'procurement_paid' => $procurement_paid,
                    'procurement_id' => $procurement_id,
                    'school_id' => $school_id
                ]);
            }
            
             // Delete existing material records
            $sql = "DELETE FROM material WHERE procurement_id = :procurement_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['procurement_id' => $procurement_id]);

            // Insert material records
            $material_name = $_POST['material_name'] ?? [];
            $material_amount = $_POST['material_amount'] ?? [];
            $material_unit = $_POST['material_unit'] ?? [];
            $material_price = $_POST['material_price'] ?? [];
            $material_prices = $_POST['material_prices'] ?? [];

            $material_list = 0;
            $total_material_cost = 0;

            for ($index = 0; $index < count($material_name); $index++) {
                if (!empty($material_name[$index]) && !empty($material_amount[$index]) && !empty($material_unit[$index])) {
                   $material_price[$index] = str_replace(',', '', $material_price[$index]);
                   $material_prices[$index] = str_replace(',', '', $material_prices[$index]);

                    $sql = "INSERT INTO material (
                                procurement_id, material_name, material_amount, material_unit, material_price, material_prices
                                ) VALUES (
                                :procurement_id, :material_name, :material_amount, :material_unit, :material_price, :material_prices
                            )";
                   $stmt = $pdo->prepare($sql);
                   $stmt->execute([
                       'procurement_id' => $procurement_id,
                       'material_name' => $material_name[$index],
                       'material_amount' => $material_amount[$index],
                       'material_unit' => $material_unit[$index],
                       'material_price' => $material_price[$index],
                       'material_prices' => $material_prices[$index]
                   ]);


                   $material_list++;
                   $total_material_cost += $material_prices[$index];
               }
           }

           if ($amount != $material_list) {
                $sql = "UPDATE procurement SET amount = :material_list, procurement_paid = :total_material_cost
                            WHERE procurement_id = :procurement_id AND school_id = :school_id";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([
                   'material_list' => $material_list,
                   'total_material_cost' => $total_material_cost,
                   'procurement_id' => $procurement_id,
                   'school_id' => $school_id
               ]);
           }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            // You might want to add more specific error handling here.
            // For example, logging errors or displaying a user-friendly message.
            exit;
        }
    }

    // Redirect logic
    $saveAndAddNew = isset($_POST['save_and_add_new']) && $_POST['save_and_add_new'] == '1';

    if ($saveAndAddNew) {
        // Save current bill then immediately open a fresh form for the next bill (same activity)
        header("Location: procurement_in.php?act_id=$activity_id&added=1");
    } else {
        // Normal save → go back to the list
        header("Location: procurement_show.php?act_id=$activity_id");
    }
    exit;
}
?>