<?php
session_name("C_Smart");
session_start();
include "./includes/connect.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>หน้าหลัก | C-Smart</title>

    <!-- Google Fonts: Sarabun (สำหรับ dashboard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>

    <!-- Sidebar -->
    <?php include "./sidebar.php"; ?>

    <!-- Main Content -->
    <main class="app-main">
        <!-- Content Header -->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0 fw-semibold">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>
                            หน้าหลัก
                        </h4>
                    </div>
                    <div class="col-auto">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">แดชบอร์ด</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="app-content">
            <div class="container-fluid">
                <?php include "./modules/main.php"; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include "./modules/footer.inc.php"; ?>

</div><!-- /.app-wrapper -->

<!-- Old IE detection (can be removed later) -->
<script>
function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    if (msie > 0) return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }
    var edge = ua.indexOf('Edge/');
    if (edge > 0) return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    return false;
}
window.onload = function() {
    if (detectIE() && detectIE() < 15) {
        alert("Browser ของคุณล้าสมัยแล้ว!! กรุณาใช้ Chrome หรือ Edge รุ่นใหม่");
        window.open("https://www.google.com/intl/th/chrome/browser/desktop/index.html", '_self');
    }
};
</script>
</body>
</html>