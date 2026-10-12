<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION["ss_school_id"]) || $_SESSION["ss_login_status"] != 1) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit();
}
include "./includes/connect.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>เพิ่มหลักสูตร | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --pink: #ec4899;
            --purple: #7c3aed;
            --bg-soft: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(15,23,42,0.08);
            --shadow-lg: 0 10px 40px rgba(79,70,229,0.15);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: linear-gradient(135deg, #f5f7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }

        /* Protect Font Awesome icons from Sarabun font override */
        .fas, .far, .fab, .fa, i[class*="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }
        .fab { font-weight: 400 !important; }
        .fas, .far { font-weight: 900 !important; }

        /* ===== Hero Header ===== */
        .form-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .form-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .form-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .form-hero .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .form-hero .hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            border: 2px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .form-hero .hero-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .form-hero .hero-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0.2rem 0 0 0;
        }

        /* ===== Form Card ===== */
        .form-card {
            background: white;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.3s ease;
        }
        .form-card:hover {
            box-shadow: var(--shadow-md);
        }
        .form-card .card-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: none;
        }
        .form-card .card-header h5 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }
        .form-card .card-body {
            padding: 2rem;
        }

        /* ===== Form Controls ===== */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .form-label .required {
            color: var(--danger);
            margin-left: 2px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid var(--border-soft);
            padding: 0.55rem 0.85rem;
            font-size: 0.92rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Select2 custom styling */
        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            border: 1.5px solid var(--border-soft);
            height: 42px;
            padding: 0.3rem 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            font-size: 0.92rem;
            color: var(--text-dark);
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }

        /* ===== Buttons ===== */
        .btn-primary-modern {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.92rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(79,70,229,0.3);
        }
        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79,70,229,0.4);
            color: white;
        }
        .btn-outline-modern {
            border: 1.5px solid var(--border-soft);
            padding: 0.6rem 2rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.92rem;
            color: var(--text-muted);
            background: white;
            transition: all 0.25s ease;
        }
        .btn-outline-modern:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: #fef2f2;
        }

        /* ===== File input ===== */
        .form-control[type="file"] {
            padding: 0.45rem 0.75rem;
        }
        .form-control[type="file"]::file-selector-button {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(79,70,229,0.2);
            padding: 0.35rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .form-control[type="file"]::file-selector-button:hover {
            background: var(--primary);
            color: white;
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-card {
            animation: fadeInUp 0.4s ease-out backwards;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .form-hero { padding: 1.25rem; }
            .form-hero .hero-title { font-size: 1.1rem; }
            .form-hero .hero-icon { width: 48px; height: 48px; font-size: 1.2rem; }
            .form-card .card-body { padding: 1.25rem; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0 fw-semibold">
                        <i class="fas fa-book text-primary me-2"></i>
                        จัดการหลักสูตร
                    </h4>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="course_main.php">หลักสูตร</a></li>
                            <li class="breadcrumb-item active">เพิ่มหลักสูตร</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- Hero Section -->
            <div class="form-hero">
                <div class="hero-content">
                    <div class="hero-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h1 class="hero-title">เพิ่มหลักสูตรการศึกษาต่อเนื่อง</h1>
                        <p class="hero-subtitle">กรอกข้อมูลหลักสูตรใหม่เพื่อบันทึกลงในระบบ C-Smart</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-card">
                        <div class="card-header">
                            <i class="fas fa-edit"></i>
                            <h5>บันทึกข้อมูลหลักสูตร</h5>
                        </div>
                        <div class="card-body">
                            <form action="course_save.php" method="post" name="form1" id="form1" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">ชื่อหลักสูตร <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="course_name" required autocomplete="off" placeholder="เช่น การเลี้ยงไก่ไข่, การทำขนมเบเกอรี่...">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">กลุ่มอาชีพ <span class="required">*</span></label>
                                        <select class="form-select" id="culture_id" name="culture_id" style="width:100%" required>
                                            <option value="" disabled selected>-- โปรดเลือก --</option>
                                            <?php
                                            try {
                                                // ดึงกลุ่มอาชีพทั้งหมดจากฐานข้อมูล culture
                                                $stmt = $pdo->query("SELECT culture_id, culture_name FROM culture ORDER BY culture_id ASC");
                                                while ($culture = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $cid  = htmlspecialchars((string)$culture['culture_id'], ENT_QUOTES, 'UTF-8');
                                                    $cname = htmlspecialchars((string)$culture['culture_name'], ENT_QUOTES, 'UTF-8');
                                                    echo "<option value=\"{$cid}\">{$cname}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("course_in.php: Error loading culture - " . $e->getMessage());
                                                echo "<option value=''>ไม่สามารถโหลดข้อมูลได้</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนชั่วโมง <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="course_hour" name="course_hour" maxlength="3" required autocomplete="off" placeholder="เช่น 40">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">สกร.ระดับอำเภอ <span class="required">*</span></label>
                                        <select class="form-select" id="school_id" name="school_id" style="width:100%" required>
                                            <option value="" disabled selected>-- โปรดเลือก --</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT * FROM school WHERE status = '2' ORDER BY school_id ASC");
                                                while ($row = $stmt->fetch()) {
                                                    echo "<option value='{$row['school_id']}'>{$row['school_name']}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                error_log("course_in.php: Error loading school - " . $e->getMessage());
                                                echo "<option value=''>ไม่สามารถโหลดข้อมูลได้</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">ไฟล์หลักสูตร (PDF) <span class="required">*</span></label>
                                        <input type="file" id="fileToUpload" name="fileToUpload" required accept=".pdf" class="form-control">
                                        <div class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>อนุญาตเฉพาะไฟล์ .PDF ขนาดไม่เกิน 5 MB
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary-modern">
                                        <i class="fas fa-save me-1"></i> บันทึก
                                    </button>
                                    <button type="button" class="btn btn-outline-modern ms-2" onclick="window.location='course_main.php';return false;">
                                        <i class="fas fa-times me-1"></i> ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "./modules/footer.inc.php"; ?>
</main>

<script>
    $(document).ready(function() {
        // Tooltip
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        // Cleave: จำนวนชั่วโมง (ตัวเลขอย่างเดียว)
        new Cleave('#course_hour', {
            numeral: true,
            numeralPositiveOnly: true,
            numeralDecimalScale: 0,
            numeralThousandsGroupStyle: 'thousand'
        });

        // Select2
        $('#culture_id, #school_id').select2({
            placeholder: '-- โปรดเลือก --',
            minimumResultsForSearch: Infinity
        });
    });

    // ตรวจสอบไฟล์ PDF ก่อนอัปโหลด
    $("#fileToUpload").change(function () {
        if (this.files && this.files.length > 0) {
            var fileExt = this.files[0].name.split('.').pop().toLowerCase();
            if (fileExt !== 'pdf') {
                alert("⚠️ ต้องเป็นไฟล์ PDF เท่านั้น");
                this.value = null;
                return;
            }
            if (this.files[0].size > 5 * 1024 * 1024) {
                alert("⚠️ ไฟล์ต้องมีขนาดไม่เกิน 5 MB");
                this.value = null;
                return;
            }
        }
    });
</script>

</body>
</html>
