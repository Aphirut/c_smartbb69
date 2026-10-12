<?php
// Minimal Test
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "DEBUG: Starting minimal test...<br>";

require_once __DIR__ . '/mpdf/vendor/autoload.php';

echo "DEBUG: Autoload loaded.<br>";

try {
    $mpdf = new \Mpdf\Mpdf();
    echo "DEBUG: mPDF instantiated without custom font config.<br>";
    $mpdf->WriteHTML('<h1>Hello World</h1>');
    echo "DEBUG: WriteHTML success.<br>";
    $mpdf->Output();
    echo "DEBUG: Output success.<br>";
} catch (\Exception $e) {
    echo "DEBUG: ERROR: " . $e->getMessage() . "<br>";
}
?>
