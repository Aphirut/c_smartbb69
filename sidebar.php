<?php
/**
 * Modern Sidebar Menu Component for AdminLTE 4
 * C-Smart Program - Enhanced UI Edition
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name("C_Smart");
    session_start();
}

// ตรวจสอบหน้าปัจจุบันเพื่อ highlight เมนู
$current_page = basename($_SERVER['PHP_SELF']);

if (!function_exists('isActive')) {
    function isActive($page, $current) {
        return (is_array($page) ? in_array($current, $page) : $page === $current) ? 'active' : '';
    }
}
if (!function_exists('isMenuOpen')) {
    function isMenuOpen($pages, $current) {
        return in_array($current, $pages) ? 'menu-open' : '';
    }
}
?>
<!-- Modern Sidebar Custom CSS -->
<link rel="stylesheet" href="./assets/css/sidebar-custom.css">
<!-- Font Awesome for sidebar icons (load last to override font-family) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- Late icon protection to override any Sarabun/body !important in page styles -->
<style>
.fas, .far, .fab, .fa, i[class*="fa-"] {
  font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
}
.fab { font-weight: 400 !important; }
.fas, .far { font-weight: 900 !important; }
</style>
<aside class="app-sidebar shadow-lg" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand">
        <a href="index.php" class="brand-link d-flex align-items-center justify-content-center py-3">
            <div class="brand-icon-wrap me-2">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="brand-text fw-bold">C-Smart <span class="text-pink">Program</span></span>
        </a>
    </div>

    <!-- Sidebar Body -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <?php if (!isset($_SESSION['ss_school_id'])): ?>
                    <!-- ============= Guest Menu ============= -->
                    <li class="nav-header">
                        <i class="fas fa-globe me-1"></i> เมนูทั่วไป
                    </li>

                    <li class="nav-item">
                        <a href="act_view_all.php" class="nav-link <?= isActive('act_view_all.php', $current_page) ?>">
                            <i class="nav-icon fas fa-child"></i>
                            <p>โครงการ</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="course_main.php" class="nav-link <?= isActive('course_main.php', $current_page) ?>">
                            <i class="nav-icon fas fa-book-open"></i>
                            <p>หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง</p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview <?= isMenuOpen(['chart_school.php','chart_activity_number.php','chart_activity_type.php','chart_activity_format.php','chart_activity_money.php','chart_sex.php','chart_skill.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                รายงานสถิติ
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="chart_school.php" class="nav-link <?= isActive('chart_school.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-chart-bar text-success"></i>
                                    <p>หน่วยงาน</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_activity_number.php" class="nav-link <?= isActive('chart_activity_number.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-chart-pie text-info"></i>
                                    <p>จำนวนโครงการ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_activity_type.php" class="nav-link <?= isActive('chart_activity_type.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-chart-pie text-danger"></i>
                                    <p>ประเภทโครงการ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_activity_format.php" class="nav-link <?= isActive('chart_activity_format.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-chart-pie text-primary"></i>
                                    <p>รูปแบบโครงการ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_activity_money.php" class="nav-link <?= isActive('chart_activity_money.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-baht-sign text-warning"></i>
                                    <p>ค่าใช้จ่ายโครงการ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_sex.php" class="nav-link <?= isActive('chart_sex.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-users text-success"></i>
                                    <p>ผู้เรียนชาย/หญิง</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="chart_skill.php" class="nav-link <?= isActive('chart_skill.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-user-graduate text-info"></i>
                                    <p>รูปแบบทักษะ</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="CSmartPhichit.pdf" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-file-pdf text-danger"></i>
                            <p>คู่มือ C-Smart</p>
                        </a>
                    </li>

                    <li class="nav-item mt-3">
                        <a href="login.php" class="nav-link btn-login-menu">
                            <i class="nav-icon fas fa-sign-in-alt"></i>
                            <p>เข้าสู่ระบบ</p>
                        </a>
                    </li>

                <?php elseif ($_SESSION['ss_login_status'] == 1): ?>
                    <!-- ============= Admin / Province Level ============= -->
                    <li class="nav-header">
                        <i class="fas fa-shield-alt me-1"></i> ผู้ดูแลระบบ
                    </li>

                    <li class="nav-item has-treeview <?= isMenuOpen(['course_main.php','course_in.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                หลักสูตร
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="course_main.php" class="nav-link <?= isActive('course_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-folder-open text-success"></i>
                                    <p>หลักสูตร</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="course_in.php" class="nav-link <?= isActive('course_in.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-plus text-danger"></i>
                                    <p>เพิ่มหลักสูตร</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item mt-3">
                        <a href="logout.php" class="nav-link btn-logout-menu">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>ออกจากระบบ</p>
                        </a>
                    </li>

                <?php else: ?>
                    <!-- ============= Normal School User ============= -->
                    <li class="nav-header">
                        <i class="fas fa-school me-1"></i> เมนูสถานศึกษา
                    </li>

                    <!-- ข้อมูล/แผน -->
                    <li class="nav-item has-treeview <?= isMenuOpen(['course_main.php','student_main.php','lecturer_main.php','act_main.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>
                                ข้อมูล/แผน
                                <span class="badge badge-stage badge-p">P</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="course_main.php" class="nav-link <?= isActive('course_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-folder-open text-success"></i>
                                    <p>หลักสูตร</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="student_main.php" class="nav-link <?= isActive('student_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-user-graduate text-info"></i>
                                    <p>ผู้เรียน</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="lecturer_main.php" class="nav-link <?= isActive('lecturer_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-chalkboard-teacher text-secondary"></i>
                                    <p>วิทยากร</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="act_main.php" class="nav-link <?= isActive('act_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-users text-warning"></i>
                                    <p>จัดตั้งกลุ่ม</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- จัดซื้อจัดจ้าง -->
                    <li class="nav-item has-treeview <?= isMenuOpen(['payee_main.php','procurement_main.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                จัดซื้อจัดจ้าง
                                <span class="badge badge-stage badge-a">A</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="payee_main.php" class="nav-link <?= isActive('payee_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-store text-danger"></i>
                                    <p>ผู้ขาย/ร้านค้า</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="procurement_main.php" class="nav-link <?= isActive('procurement_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-file-invoice-dollar text-warning"></i>
                                    <p>จัดซื้อจัดจ้าง</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- อนุมัติ (เฉพาะ login_status == 2) -->
                    <?php if (isset($_SESSION['ss_login_status']) && $_SESSION['ss_login_status'] == 2): ?>
                    <li class="nav-item has-treeview <?= isMenuOpen(['approve_main.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>
                                ดำเนินการ-อนุมัติ
                                <span class="badge badge-stage badge-w">W</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="approve_main.php" class="nav-link <?= isActive('approve_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-folder-open text-danger"></i>
                                    <p>อนุมัติ-เปิดกลุ่ม</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- กิจกรรม/เอกสาร -->
                    <li class="nav-item has-treeview <?= isMenuOpen(['paper_open.php','paper_time.php','payment_main.php','paper_satisfied.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>
                                กิจกรรม/เอกสาร
                                <span class="badge badge-stage badge-d">D</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="paper_open.php" class="nav-link <?= isActive('paper_open.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-file text-primary"></i>
                                    <p>เอกสารเปิดกลุ่ม</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="paper_time.php" class="nav-link <?= isActive('paper_time.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-clock text-success"></i>
                                    <p>เอกสารลงเวลา</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="payment_main.php" class="nav-link <?= isActive('payment_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-money-bill-wave text-danger"></i>
                                    <p>เอกสารการเงิน</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="paper_satisfied.php" class="nav-link <?= isActive('paper_satisfied.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-star text-secondary"></i>
                                    <p>ประเมินความพึงพอใจ</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ประเมินผล/รายงาน -->
                    <li class="nav-item has-treeview <?= isMenuOpen(['assess_main.php','result_main.php','reporting_main.php'], $current_page) ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                ประเมินผล/รายงาน
                                <span class="badge badge-stage badge-c">C</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="assess_main.php" class="nav-link <?= isActive('assess_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-clipboard-check text-primary"></i>
                                    <p>การประเมินผลฯ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="result_main.php" class="nav-link <?= isActive('result_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-file-alt text-success"></i>
                                    <p>รายงานผลการจัดฯ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="reporting_main.php" class="nav-link <?= isActive('reporting_main.php', $current_page) ?>">
                                    <i class="nav-icon fas fa-images text-danger"></i>
                                    <p>รายงานภาพการจัดฯ</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- หมวด: เอกสารส่งออก -->
                    <li class="nav-header mt-2">
                        <i class="fas fa-file-export me-1"></i> เอกสารส่งออก
                    </li>

                    <!-- เอกสาร Word -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-word"></i>
                            <p>
                                เอกสาร Word
                                <span class="badge badge-stage badge-w">W</span>
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="assess_main_word.php" class="nav-link">
                                    <i class="nav-icon fas fa-file-word text-primary"></i>
                                    <p>เอกสารเปิดกลุ่ม</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="result_main.php" class="nav-link">
                                    <i class="nav-icon fas fa-file-word text-success"></i>
                                    <p>เอกสารลงเวลา</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="reporting_main.php" class="nav-link">
                                    <i class="nav-icon fas fa-file-word text-danger"></i>
                                    <p>เอกสารการเงิน</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="report_lecturer_word.php" class="nav-link">
                                    <i class="nav-icon fas fa-file-invoice-dollar text-danger"></i>
                                    <p>เบิกเงินวิทยากร</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ดาวน์โหลดเอกสาร -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cloud-download-alt"></i>
                            <p>
                                ดาวน์โหลดเอกสาร
                                <i class="nav-arrow fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="https://drive.google.com/drive/folders/19O7HDkHzyIDWvsh6CASFH1IM9koxMUro?usp=sharing" target="_blank" rel="noopener" class="nav-link">
                                    <i class="nav-icon fas fa-download text-danger"></i>
                                    <p>เบิกค่าใช้จ่ายฯ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="https://drive.google.com/drive/folders/1QP0BgBWW4okbWgYBN4CliqsMIlOPEkBN?usp=sharing" target="_blank" rel="noopener" class="nav-link">
                                    <i class="nav-icon fas fa-download text-success"></i>
                                    <p>เบิกค่าตอบแทนฯ</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="https://drive.google.com/drive/folders/1OsDD9K9Fc2edURGGX_qGw77SU6uunaHj?usp=sharing" target="_blank" rel="noopener" class="nav-link">
                                    <i class="nav-icon fas fa-download text-warning"></i>
                                    <p>แบบฟอร์ม พต.</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="https://drive.google.com/drive/folders/1yd13ipDlh4f1cpJuUHWXYUIJY8nfCUpR?usp=sharing" target="_blank" rel="noopener" class="nav-link">
                                    <i class="nav-icon fas fa-download text-info"></i>
                                    <p>เอกสารจัดซื้อ/จัดจ้าง</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item mt-3">
                        <a href="logout.php" class="nav-link btn-logout-menu">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>ออกจากระบบ</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <small>
            <i class="fas fa-code text-pink"></i> C-Smart © <?= date('Y') ?>
        </small>
    </div>
</aside>