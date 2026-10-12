<?php
session_name("C_Smart");
session_start();
if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
} else {
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php
    include "./modules/head.inc.php";
    include "./includes/connect.php";
    include "./includes/function.php";
    ?>
    <title>ข้อมูลวิทยากร | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Select2 Bootstrap 5 theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
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

        /* ===== Breadcrumb ===== */
        .modern-breadcrumb {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }
        .modern-breadcrumb .breadcrumb { margin: 0; }
        .modern-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

        /* ===== Hero Header ===== */
        .lecturer-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .lecturer-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .lecturer-hero::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .hero-avatar {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .hero-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .hero-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        .btn-hero-back {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.6rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-hero-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: translateX(-3px);
        }
        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* ===== Section Cards ===== */
        .section-card {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }
        .section-card:hover {
            box-shadow: var(--shadow-md);
            border-color: rgba(79,70,229,0.2);
        }
        .section-card-header {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to right, #fafbff, white);
        }
        .section-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: white;
        }
        .section-icon-wrap.bg-personal { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .section-icon-wrap.bg-work     { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .section-icon-wrap.bg-address  { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .section-icon-wrap.bg-finance  { background: linear-gradient(135deg, #10b981, #059669); }
        .section-icon-wrap.bg-register { background: linear-gradient(135deg, #ec4899, #db2777); }
        
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }
        .section-subtitle {
            font-size: 0.825rem;
            color: var(--text-muted);
            margin: 0;
        }
        .section-badge {
            margin-left: auto;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-weight: 600;
        }
        .section-badge.optional {
            background: #f1f5f9;
            color: var(--text-muted);
        }
        .section-card-body {
            padding: 1.5rem;
        }

        /* ===== Floating Form Controls ===== */
        .form-floating-modern {
            position: relative;
        }
        .form-floating-modern > .form-control,
        .form-floating-modern > .form-select {
            height: 58px;
            padding: 1.3rem 1rem 0.4rem 2.85rem !important;
            border-radius: 12px;
            border: 1.5px solid var(--border-soft);
            font-size: 0.95rem;
            background: white;
            transition: all 0.25s ease;
        }
        .form-floating-modern > .form-control:focus,
        .form-floating-modern > .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
            background: white;
        }
        .form-floating-modern > label {
            position: absolute;
            top: 0;
            left: 2.6rem;
            padding: 1rem 0.25rem;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            pointer-events: none;
            transition: all 0.2s ease;
            transform-origin: 0 0;
        }
        .form-floating-modern > .form-control:focus ~ label,
        .form-floating-modern > .form-control:not(:placeholder-shown) ~ label,
        .form-floating-modern > .form-select ~ label {
            transform: scale(0.78) translateY(-0.6rem);
            color: var(--primary);
            font-weight: 600;
        }
        .form-floating-modern .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1rem;
            z-index: 3;
            pointer-events: none;
        }
        .form-floating-modern .invalid-feedback {
            margin-left: 0.5rem;
            font-size: 0.825rem;
            font-weight: 500;
        }

        /* Select2 with bootstrap-5 theme modernize */
        .select2-container--bootstrap-5 .select2-selection,
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 58px !important;
            border: 1.5px solid var(--border-soft) !important;
            border-radius: 12px !important;
            background-color: white !important;
            padding: 1.3rem 1rem 0.4rem 2.4rem !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: var(--text-dark);
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 56px !important;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12) !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        /* ===== Sticky Action Bar ===== */
        .action-bar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: 0 -4px 20px rgba(15,23,42,0.06);
            border: 1px solid var(--border-soft);
            position: sticky;
            bottom: 1rem;
            z-index: 100;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: flex-end;
            align-items: center;
        }
        .action-bar-info {
            margin-right: auto;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        .action-bar-info i { color: var(--warning); margin-right: 0.35rem; }

        .btn-modern {
            padding: 0.7rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary-grad {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(79,70,229,0.35);
        }
        .btn-primary-grad:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,70,229,0.45);
            color: white;
        }
        .btn-outline-modern {
            background: var(--bg-soft);
            color: var(--text-dark);
            border: 1.5px solid var(--border-soft);
        }
        .btn-outline-modern:hover {
            background: white;
            border-color: var(--text-muted);
            color: var(--text-dark);
        }

        /* Hint text */
        .hint-text {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
            margin-left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .lecturer-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .section-card-body { padding: 1.1rem; }
            .action-bar { flex-direction: column-reverse; }
            .action-bar .btn-modern { width: 100%; justify-content: center; }
            .action-bar-info { width: 100%; text-align: center; margin: 0 0 0.5rem 0; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .section-card { animation: fadeInUp 0.4s ease-out backwards; }
        .section-card:nth-of-type(1) { animation-delay: 0.05s; }
        .section-card:nth-of-type(2) { animation-delay: 0.1s; }
        .section-card:nth-of-type(3) { animation-delay: 0.15s; }
        .section-card:nth-of-type(4) { animation-delay: 0.2s; }
        .section-card:nth-of-type(5) { animation-delay: 0.25s; }
    </style>

    <?php
    /**
     * ฟังก์ชันแยกที่อยู่ภาษาไทย
     */
    function parseThaiAddress($address) {
        $result = [
            'house_number' => '', 'village_no' => '', 'subdistrict' => '',
            'district' => '', 'province' => '', 'postal_code' => ''
        ];
        if (empty($address)) return $result;
        
        $address = trim($address);
        
        if (preg_match('/(\d{5})\s*$/', $address, $matches)) {
            $result['postal_code'] = $matches[1];
            $address = preg_replace('/\s*' . preg_quote($matches[1]) . '\s*$/', '', $address);
        }
        if (preg_match('/(?:จ\.?|จังหวัด)\s*([^\s,]+)/', $address, $matches)) {
            $result['province'] = trim($matches[1]);
            $address = preg_replace('/\s*(?:จ\.?|จังหวัด)\s*' . preg_quote($matches[1]) . '\s*/', '', $address);
        }
        if (preg_match('/(?:อ\.?|อำเภอ|เขต)\s*([^\s,]+)/', $address, $matches)) {
            $result['district'] = trim($matches[1]);
            $address = preg_replace('/\s*(?:อ\.?|อำเภอ|เขต)\s*' . preg_quote($matches[1]) . '\s*/', '', $address);
        }
        if (preg_match('/(?:ต\.?|ตำบล|แขวง)\s*([^\s,]+)/', $address, $matches)) {
            $result['subdistrict'] = trim($matches[1]);
            $address = preg_replace('/\s*(?:ต\.?|ตำบล|แขวง)\s*' . preg_quote($matches[1]) . '\s*/', '', $address);
        }
        if (preg_match('/(?:หมู่|ม\.?)\s*(\d+)/', $address, $matches)) {
            $result['village_no'] = trim($matches[1]);
            $address = preg_replace('/\s*(?:หมู่|ม\.?)\s*' . preg_quote($matches[1]) . '\s*/', '', $address);
        }
        
        $remaining = trim(preg_replace(['/[,\s]+/', '/^[\/\-,\s]+|[\/\-,\s]+$/'], [' ', ''], $address));
        if (!empty($remaining)) {
            $result['house_number'] = $remaining;
        }
        
        return $result;
    }

    $edit_id = isset($_REQUEST['edit_id']) ? $_REQUEST['edit_id'] : '';
    
    $lecturer_id = $firstname = $lastname = $idcard = $birthday = $education_id = $career_id = "";
    $house_number = $village_no = $village_name = $subdistrict = $district = $province = $postal_code = "";
    $tel = $register = $talent = $bank_name = $branch = $Account_Number = "";

    $banks = ["ธนาคารกรุงไทย", "ธนาคารทีทีบี", "ธนาคารไทยพาณิชย์", "ธนาคารออมสิน", "ธนาคารยูโอบี", "ธนาคารกสิกรไทย", "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร"];

    if ($edit_id != "") {
        try {
            $stmt = $pdo->prepare("SELECT * FROM lecturer WHERE lecturer_id = :edit_id AND school_id = :school_id LIMIT 1");
            $stmt->execute(['edit_id' => $edit_id, 'school_id' => $_SESSION['ss_school_id']]);
            
            if ($stmt->rowCount() > 0 && is_numeric($edit_id)) {
                $row = $stmt->fetch();
                $lecturer_id = $row['lecturer_id'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $idcard = $row['idcard'];
                $birthday = ADYearToBEYear($row['birthday']);
                $education_id = $row['education_id'];
                $career_id = $row['career_id'];
                
                $village_name = $row['village_name'] ?? '';

                if (isset($row['house_number']) && !empty($row['house_number'])) {
                    $house_number = $row['house_number'];
                    $village_no = $row['village_no'] ?? '';
                    $subdistrict = $row['subdistrict'] ?? '';
                    $district = $row['district'] ?? '';
                    $province = $row['province'] ?? '';
                    $postal_code = $row['postal_code'] ?? '';
                } else {
                    $address_parts = parseThaiAddress($row['lecturer_address']);
                    extract($address_parts);
                }
                
                $tel = $row['tel'];
                $register = ADYearToBEYear($row['register']);
                $talent = $row['talent'];
                $bank_name = $row['bank_name'] ?? ''; 
                $branch = $row['branch'] ?? '';
                $Account_Number = $row['Account_Number'] ?? ''; 
            } else {
                echo "<META HTTP-EQUIV='REFRESH' CONTENT='0;url=index.php'>";
                exit();
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    $isEdit = ($lecturer_id != "");
    ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="lecturer_main.php">ข้อมูลวิทยากร</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?= $isEdit ? "แก้ไขข้อมูลวิทยากร" : "เพิ่มข้อมูลวิทยากร"; ?></li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="lecturer-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-<?= $isEdit ? 'user-edit' : 'chalkboard-teacher'; ?>"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-<?= $isEdit ? 'pen' : 'plus-circle'; ?>"></i>
                                <?= $isEdit ? 'โหมดแก้ไข' : 'โหมดเพิ่มใหม่'; ?>
                            </span>
                            <h1 class="hero-title mt-2"><?= $isEdit ? "แก้ไขข้อมูลวิทยากร" : "เพิ่มข้อมูลวิทยากร"; ?></h1>
                            <p class="hero-subtitle">กรุณากรอกข้อมูลให้ครบถ้วน เพื่อบันทึกในระบบ C-Smart</p>
                        </div>
                    </div>
                    <a href="lecturer_main.php" class="btn-hero-back">
                        <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อ
                    </a>
                </div>
            </div>

            <!-- Form Start -->
            <form class="form" action="lecturer_save.php<?php if($lecturer_id != ""){echo "?submit=EDIT";}?>" method="post" name="form1" id="form1" novalidate>

                <!-- Section 1: ข้อมูลส่วนตัว -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-personal">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ข้อมูลส่วนตัว</h6>
                            <p class="section-subtitle">ชื่อ-นามสกุล บัตรประชาชน และวันเกิด</p>
                        </div>
                        <span class="section-badge"><i class="fas fa-circle-info me-1"></i>จำเป็น</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- ชื่อ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($firstname); ?>">
                                    <label for="firstname">ชื่อ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกชื่อ</div>
                                </div>
                            </div>
                            <!-- นามสกุล -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($lastname); ?>">
                                    <label for="lastname">นามสกุล <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกนามสกุล</div>
                                </div>
                            </div>
                            <!-- บัตรประชาชน -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" class="form-control" id="idcard" name="idcard"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($idcard); ?>">
                                    <label for="idcard">เลขบัตรประชาชน <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกเลขบัตรประชาชนให้ถูกต้อง</div>
                                </div>
                            </div>
                            <!-- วันเกิด -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-birthday-cake input-icon"></i>
                                    <input type="text" class="form-control datepicker" id="birthday" name="birthday"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($birthday); ?>">
                                    <label for="birthday">วัน/เดือน/ปีเกิด (พ.ศ.) <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกวันเกิด</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลการทำงานและความเชี่ยวชาญ -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-work">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ข้อมูลการทำงานและความเชี่ยวชาญ</h6>
                            <p class="section-subtitle">ระดับการศึกษา อาชีพ และความสามารถพิเศษ</p>
                        </div>
                        <span class="section-badge"><i class="fas fa-star me-1"></i>สำคัญ</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- การศึกษา -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-graduation-cap input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field" id="education_id" name="education_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $result_edu = $pdo->query("SELECT * FROM education ORDER BY education_id ASC");
                                            while ($row_select = $result_edu->fetch()) { 
                                                $selected = ($row_select['education_id'] == $education_id) ? "selected" : "";
                                                echo "<option value='" . htmlspecialchars($row_select['education_id']) . "' $selected>" . htmlspecialchars($row_select['education_name']) . "</option>";
                                            }
                                        } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
                                        ?>
                                    </select>
                                    <label for="education_id">ระดับการศึกษา <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกระดับการศึกษา</div>
                                </div>
                            </div>
                            <!-- อาชีพ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-user-tie input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field" id="career_id" name="career_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $result_career = $pdo->query("SELECT * FROM career ORDER BY career_id ASC");
                                            while ($row_select = $result_career->fetch()) { 
                                                $selected = ($row_select['career_id'] == $career_id) ? "selected" : "";
                                                echo "<option value='" . htmlspecialchars($row_select['career_id']) . "' $selected>" . htmlspecialchars($row_select['career_name']) . "</option>";
                                            }
                                        } catch (PDOException $e) { echo "Error: " . $e->getMessage(); }
                                        ?>
                                    </select>
                                    <label for="career_id">อาชีพ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกอาชีพ</div>
                                </div>
                            </div>
                            <!-- ความเชี่ยวชาญ -->
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="fas fa-star input-icon"></i>
                                    <input type="text" class="form-control" id="talent" name="talent"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($talent); ?>">
                                    <label for="talent">ความเชี่ยวชาญ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกความเชี่ยวชาญ</div>
                                </div>
                                <div class="hint-text">
                                    <i class="fas fa-lightbulb"></i>
                                    ตัวอย่าง: ภาษาอังกฤษ, การเกษตร, คอมพิวเตอร์, งานช่าง ฯลฯ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: ที่อยู่และการติดต่อ -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-address">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ที่อยู่และการติดต่อ</h6>
                            <p class="section-subtitle">ที่อยู่ปัจจุบันและเบอร์โทรศัพท์</p>
                        </div>
                        <span class="section-badge"><i class="fas fa-phone me-1"></i>ติดต่อ</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- บ้านเลขที่ -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-home input-icon"></i>
                                    <input type="text" class="form-control" id="house_number" name="house_number"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($house_number); ?>">
                                    <label for="house_number">บ้านเลขที่ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกบ้านเลขที่</div>
                                </div>
                            </div>
                            <!-- หมู่ที่ -->
                            <div class="col-md-2">
                                <div class="form-floating-modern">
                                    <i class="fas fa-hashtag input-icon"></i>
                                    <input type="text" class="form-control" id="village_no" name="village_no"
                                           autocomplete="off" placeholder=" "
                                           value="<?= htmlspecialchars($village_no); ?>">
                                    <label for="village_no">หมู่ที่</label>
                                </div>
                            </div>
                            <!-- ชื่อหมู่บ้าน -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-torii-gate input-icon"></i>
                                    <input type="text" class="form-control" id="village_name" name="village_name"
                                           autocomplete="off" placeholder=" "
                                           value="<?= htmlspecialchars($village_name); ?>">
                                    <label for="village_name">ชื่อหมู่บ้าน</label>
                                    <div class="invalid-feedback">กรุณากรอกชื่อหมู่บ้าน</div>
                                </div>
                            </div>
                            <!-- จังหวัด -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-flag input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field addr-select" id="province" name="province">
                                        <option value="">-- โปรดเลือก --</option>
                                    </select>
                                    <label for="province">จังหวัด <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกจังหวัด</div>
                                </div>
                            </div>
                            <!-- อำเภอ -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-map input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field addr-select" id="district" name="district">
                                        <option value="">-- โปรดเลือกจังหวัดก่อน --</option>
                                    </select>
                                    <label for="district">อำเภอ/เขต <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกอำเภอ/เขต</div>
                                </div>
                            </div>
                            <!-- ตำบล -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-map-pin input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field addr-select" id="subdistrict" name="subdistrict">
                                        <option value="">-- โปรดเลือกอำเภอก่อน --</option>
                                    </select>
                                    <label for="subdistrict">ตำบล/แขวง <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกตำบล/แขวง</div>
                                </div>
                            </div>
                            <!-- รหัสไปรษณีย์ -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-mail-bulk input-icon"></i>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($postal_code); ?>">
                                    <label for="postal_code">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกรหัสไปรษณีย์ 5 หลัก</div>
                                </div>
                            </div>
                            <!-- เบอร์โทร -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" class="form-control" id="tel" name="tel"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($tel); ?>">
                                    <label for="tel">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกเบอร์โทรศัพท์</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: ข้อมูลการเงิน -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-finance">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ข้อมูลการเงิน</h6>
                            <p class="section-subtitle">บัญชีธนาคารสำหรับโอนค่าตอบแทน</p>
                        </div>
                        <span class="section-badge optional"><i class="fas fa-info-circle me-1"></i>ไม่บังคับ</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- ธนาคาร -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-university input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2-field" id="bank_name" name="bank_name">
                                        <option value="">-- โปรดเลือกธนาคาร --</option>
                                        <?php
                                        foreach ($banks as $current_bank) {
                                            $selected = ($current_bank == $bank_name) ? "selected" : "";
                                            echo "<option value=\"" . htmlspecialchars($current_bank) . "\" $selected>" . htmlspecialchars($current_bank) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <label for="bank_name">ธนาคาร</label>
                                </div>
                            </div>
                            <!-- สาขา -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-building input-icon"></i>
                                    <input type="text" class="form-control" id="branch" name="branch"
                                           autocomplete="off" placeholder=" "
                                           value="<?= htmlspecialchars($branch); ?>">
                                    <label for="branch">สาขา</label>
                                </div>
                            </div>
                            <!-- เลขที่บัญชี -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-credit-card input-icon"></i>
                                    <input type="text" class="form-control" id="Account_Number" name="Account_Number"
                                           autocomplete="off" placeholder=" "
                                           value="<?= htmlspecialchars($Account_Number); ?>">
                                    <label for="Account_Number">เลขที่บัญชี</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: วันที่สมัคร -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-register">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="section-title">วันที่สมัครเป็นวิทยากร</h6>
                            <p class="section-subtitle">ระบุวันที่เริ่มต้นในระบบ</p>
                        </div>
                        <span class="section-badge"><i class="fas fa-clock me-1"></i>จำเป็น</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <input type="text" class="form-control datepicker" id="register" name="register"
                                           autocomplete="off" required placeholder=" "
                                           value="<?= htmlspecialchars($register); ?>">
                                    <label for="register">วันที่สมัคร (พ.ศ.) <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณากรอกวันที่สมัคร</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Action Bar -->
                <div class="action-bar">
                    <div class="action-bar-info">
                        <i class="fas fa-exclamation-circle"></i>
                        กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนบันทึก
                    </div>
                    <a href="lecturer_main.php" class="btn btn-modern btn-outline-modern">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-modern btn-primary-grad">
                        <i class="fas fa-save"></i>
                        <?= $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล'; ?>
                    </button>
                    <input type="hidden" id="lecturer_id" name="lecturer_id" value="<?= htmlspecialchars($lecturer_id); ?>">
                </div>

            </form>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    // ===== ฟังก์ชันช่วยเหลือสำหรับที่อยู่แบบกรองข้อมูล =====
    function addrEscape(text) {
        return $('<div>').text(text === undefined || text === null ? '' : text).html();
    }

    function resetAddrSelect($el, placeholder) {
        $el.html('<option value="">' + addrEscape(placeholder) + '</option>');
        $el.val('');
        $el.trigger('change.select2');
    }

    function loadAddrOptions($el, url, params, done) {
        $.getJSON(url, params || {})
            .done(function(data) {
                if (!$.isArray(data)) data = [];

                var html = '<option value="">-- โปรดเลือก --</option>';
                $.each(data, function(i, item) {
                    var zip = (item.zip_code !== undefined && item.zip_code !== null && item.zip_code !== '')
                        ? ' data-zip="' + addrEscape(item.zip_code) + '"'
                        : '';
                    var name = addrEscape(item.name_th);
                    html += '<option value="' + name + '" data-id="' + addrEscape(item.id) + '"' + zip + '>' + name + '</option>';
                });
                $el.html(html);
                $el.trigger('change.select2');
            })
            .always(function() {
                if (typeof done === 'function') done();
            });
    }

    // ตัดคำนำหน้าหน่วยปกครองออก เพื่อเทียบค่าที่เก็บไว้กับรายการใน dropdown
    // เช่น "อำเภอบ้านบึง" กับ "บ้านบึง" ถือว่าเป็นค่าเดียวกัน
    function addrNormalize(text) {
        var s = $.trim(text || '');
        var prefixes = ['จังหวัด', 'อำเภอ', 'ตำบล', 'แขวง', 'เขต', 'กรุงเทพมหานคร', 'จ.', 'อ.', 'ต.'];
        var changed = true;
        while (changed) {
            changed = false;
            for (var i = 0; i < prefixes.length; i++) {
                var p = prefixes[i];
                if (s.indexOf(p) === 0 && s.length > p.length) {
                    s = $.trim(s.substring(p.length));
                    changed = true;
                    break;
                }
            }
        }
        return s;
    }

    function addrFindOptionValue($el, stored) {
        if (!stored) return '';
        var want = addrNormalize(stored);
        var found = '';
        $el.find('option').each(function() {
            if (addrNormalize($(this).val()) === want) {
                found = $(this).val();
                return false;
            }
        });
        return found;
    }

    // เพิ่มค่าที่เก็บไว้เป็นตัวเลือกสำรอง กรณีไม่ตรงกับรายการมาตรฐาน (เช่น ข้อมูลเดิมพิมพ์ตกหล่น)
    function addrAppendOption($el, value) {
        var name = $.trim(value);
        if (!name) return '';
        if (!addrFindOptionValue($el, name)) {
            $el.append($('<option>').val(name).text(name).attr('data-custom', '1'));
            $el.trigger('change.select2');
        }
        return name;
    }

    function ADYearToBEYear(dateString) {
        if (!dateString || dateString.trim() === '') return '';
        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString; 

        let day = parts[0].padStart(2, '0');
        let month = parts[1].padStart(2, '0');
        let year = parseInt(parts[2], 10);

        if (isNaN(year)) return dateString;

        if (year < 2400) { 
            year += 543;
        }
        return `${day}/${month}/${year}`;
    }

    function BEYearToADYear(dateString) {
        if (!dateString || dateString.trim() === '') return '';        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString; 

        let day = parts[0].padStart(2, '0');
        let month = parts[1].padStart(2, '0');
        let year = parseInt(parts[2], 10);
        
        if (isNaN(year)) return dateString; 

        if (year > 2400) { 
            year -= 543;
        }
        return `${day}/${month}/${year}`;
    }

    $(document).ready(function () {
        // Select2 init
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-field').select2({
                placeholder: '-- โปรดเลือก --',
                theme: "bootstrap-5",
                width: '100%'
            });
        }

        // ===== ที่อยู่แบบกรองข้อมูล: จังหวัด → อำเภอ → ตำบล → รหัสไปรษณีย์ =====
        var editAddr = {
            province:    <?= json_encode($province,    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
            district:    <?= json_encode($district,    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
            subdistrict: <?= json_encode($subdistrict, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
        };

        // โหลดจังหวัด แล้วไล่โหลดอำเภอ/ตำบลตามข้อมูลเดิม (โหมดแก้ไข)
        loadAddrOptions($('#province'), 'get_provinces.php', {}, function () {
            var $province = $('#province');

            if (editAddr.province) {
                var pVal = addrFindOptionValue($province, editAddr.province) || addrAppendOption($province, editAddr.province);
                $province.val(pVal).trigger('change.select2');
            }
            if (!$province.val()) return;

            loadAddrOptions($('#district'), 'get_amphures.php', {
                province_id: $province.find(':selected').data('id')
            }, function () {
                var $district = $('#district');

                if (editAddr.district) {
                    var dVal = addrFindOptionValue($district, editAddr.district) || addrAppendOption($district, editAddr.district);
                    $district.val(dVal).trigger('change.select2');
                }
                if (!$district.val()) return;

                loadAddrOptions($('#subdistrict'), 'get_tambons.php', {
                    district_id: $district.find(':selected').data('id')
                }, function () {
                    var $subdistrict = $('#subdistrict');

                    if (editAddr.subdistrict) {
                        var sVal = addrFindOptionValue($subdistrict, editAddr.subdistrict) || addrAppendOption($subdistrict, editAddr.subdistrict);
                        $subdistrict.val(sVal).trigger('change.select2');
                    }

                    // ถ้ายังไม่มีรหัสไปรษณีย์เดิม ให้เติมจากตำบลที่เลือก
                    if (!$.trim($('#postal_code').val())) {
                        var zip = $subdistrict.find(':selected').data('zip');
                        if (zip) $('#postal_code').val(zip);
                    }
                });
            });
        });

        $('#province').on('change', function () {
            resetAddrSelect($('#district'), '-- โปรดเลือกจังหวัดก่อน --');
            resetAddrSelect($('#subdistrict'), '-- โปรดเลือกอำเภอก่อน --');
            $('#postal_code').val('');

            var provinceId = $(this).find(':selected').data('id');
            if (!provinceId) return;
            loadAddrOptions($('#district'), 'get_amphures.php', { province_id: provinceId });
        });

        $('#district').on('change', function () {
            resetAddrSelect($('#subdistrict'), '-- โปรดเลือกอำเภอก่อน --');
            $('#postal_code').val('');

            var districtId = $(this).find(':selected').data('id');
            if (!districtId) return;
            loadAddrOptions($('#subdistrict'), 'get_tambons.php', { district_id: districtId });
        });

        $('#subdistrict').on('change', function () {
            var zip = $(this).find(':selected').data('zip');
            if (zip) $('#postal_code').val(zip);
        });

        // ตรวจสอบที่อยู่ก่อนส่งฟอร์ม (select2 ทำให้การแจ้งเตือนด้วย required ของ select แสดงไม่ได้)
        $('.form').on('submit', function (e) {
            var missing = [];
            if (!$('#province').val())    missing.push('จังหวัด');
            if (!$('#district').val())    missing.push('อำเภอ/เขต');
            if (!$('#subdistrict').val()) missing.push('ตำบล/แขวง');
            if (missing.length === 0) return;

            e.preventDefault();
            e.stopPropagation();
            alert('กรุณาเลือก: ' + missing.join(', '));

            var firstId = !$('#province').val() ? '#province' : (!$('#district').val() ? '#district' : '#subdistrict');
            var $first = $(firstId);
            var $box = $first.closest('.form-floating-modern');
            if ($box.length) {
                $('html, body').animate({ scrollTop: $box.offset().top - 120 }, 200);
            }
            if ($first.data('select2')) $first.select2('open');
        });

        // Datepicker
        var datepickerElements = $('.datepicker');
        if (datepickerElements.length > 0 && typeof $.fn.datepicker !== 'undefined') {
            $.datepicker.regional['th'] = {
                closeText: 'ปิด', prevText: '< ย้อน', nextText: 'ถัดไป >', currentText: 'วันนี้',
                monthNames: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน', 'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
                monthNamesShort: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.', 'ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
                dayNames: ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'],
                dayNamesShort: ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],
                dayNamesMin: ['อา','จ','อ','พ','พฤ','ศ','ส'],
                weekHeader: 'Wk', dateFormat: 'dd/mm/yy', firstDay: 0, isRTL: false, showMonthAfterYear: false, yearSuffix: '',
                beforeShow: function(input, inst) {
                    var currentValue = $(input).val();
                    if (currentValue) {
                        let parts = currentValue.split('/');
                        if (parts.length === 3) {
                            let year = parseInt(parts[2], 10);
                            if (!isNaN(year) && year > 2400) { 
                                $(input).datepicker('setDate', BEYearToADYear(currentValue));
                            }
                        }
                    }
                    setTimeout(function(){
                        $.each($(".ui-datepicker-year option"), function(j, k) {
                            var currentOptionYear = parseInt($(k).text(), 10);
                            if (!isNaN(currentOptionYear) && currentOptionYear < 2500) { 
                                $(k).text(currentOptionYear + 543);
                            }
                        });
                    }, 50);
                },
                onChangeMonthYear: function(year, month, inst) {
                    setTimeout(function(){
                        $.each($(".ui-datepicker-year option"), function(j, k) {
                            var currentOptionYear = parseInt($(k).text(), 10);
                            if (!isNaN(currentOptionYear) && currentOptionYear < 2500) { 
                                $(k).text(currentOptionYear + 543);
                            }
                        });
                    }, 50);
                },
                onSelect: function(dateText, inst) {
                    var adDateParts = dateText.split('/'); 
                    if (adDateParts.length === 3) {
                        var adYear = parseInt(adDateParts[2], 10);
                        if (!isNaN(adYear)) {
                            var beYear = adYear + 543;
                            $(this).val(adDateParts[0].padStart(2,'0') + '/' + adDateParts[1].padStart(2,'0') + '/' + beYear);
                        }
                    }
                }
            };
            
            $.datepicker.setDefaults($.datepicker.regional['th']);
            datepickerElements.datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+10', 
            });

            datepickerElements.each(function() {
                var currentValue = $(this).val();
                if (currentValue) {
                    var adDate = BEYearToADYear(currentValue); 
                    $(this).datepicker('setDate', adDate); 
                    var parts = currentValue.split('/');
                    if(parts.length === 3 && parseInt(parts[2], 10) > 2400){
                         $(this).val(currentValue); 
                    }
                }
            });
        }

        // Cleave formatters
        new Cleave('#idcard', { numericOnly: true, delimiter: '-', blocks: [1, 4, 5, 2, 1] });
        new Cleave('#tel', { numericOnly: true, delimiter: '-', blocks: [3, 3, 4] });
        new Cleave('#birthday', { date: true, delimiter: '/', datePattern: ['d', 'm', 'Y'] });
        new Cleave('#register', { date: true, delimiter: '/', datePattern: ['d', 'm', 'Y'] });
        new Cleave('#postal_code', { numericOnly: true, blocks: [5] });
        new Cleave('#village_no', { numericOnly: true, blocks: [2] });
        new Cleave('#Account_Number', { numericOnly: true, delimiter: '-', blocks: [3, 1, 5, 1] });
        
        // Bootstrap 5 Form Validation
        (() => {
          'use strict'
          const forms = document.querySelectorAll('.form')
          Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
              const idcardInput = document.getElementById('idcard');
              const postalInput = document.getElementById('postal_code');
              
              idcardInput.setCustomValidity("");
              postalInput.setCustomValidity("");

              if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
              }
              
              const idcard = idcardInput.value.replace(/(_)|(-)/g, '');
              if (idcard.length > 0 && idcard.length < 13) { 
                idcardInput.setCustomValidity("เลขบัตรประชาชนไม่ครบ 13 หลัก");
                event.preventDefault(); event.stopPropagation(); 
              } else if (idcard.length === 13) {
                let sum = 0;
                for (let i = 0; i < 12; i++) {
                    sum += parseFloat(idcard.charAt(i)) * (13 - i);
                }
                if ((11 - sum % 11) % 10 !== parseFloat(idcard.charAt(12))) {
                    idcardInput.setCustomValidity("เลขบัตรประชาชนไม่ถูกต้อง");
                    event.preventDefault(); event.stopPropagation(); 
                } else {
                    idcardInput.setCustomValidity("");
                    $.ajax({
                        type: "POST",
                        url: "./ajax/lecturer_idcard_check.php",
                        data: { lecturer_id: $('#lecturer_id').val(), idcard: idcard },
                        async: false,
                        success: function(result) {
                            var isDuplicate = false;
                            try {
                                isDuplicate = (JSON.parse(result) === false);
                            } catch(e) {
                                isDuplicate = (result.toString().trim() === "false");
                            }
                            if (isDuplicate) {
                                idcardInput.setCustomValidity("เลขบัตรประชาชนซ้ำ");
                                event.preventDefault(); event.stopPropagation();
                            }
                        },
                        error: function() {
                           idcardInput.setCustomValidity("ไม่สามารถตรวจสอบเลขบัตรประชาชนได้");
                           event.preventDefault(); event.stopPropagation();
                        }
                    });
                }
              }

              const postalCode = postalInput.value.replace(/\D/g, '');
              if (postalCode.length > 0 && postalCode.length !== 5) {
                postalInput.setCustomValidity("รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก");
                event.preventDefault(); event.stopPropagation();
              } else {
                postalInput.setCustomValidity("");
              }
              
              form.classList.add('was-validated')
            }, false)
          })
        })()

        $('#idcard, #postal_code').on('input', function(e){
            e.target.setCustomValidity(""); 
        });
    });
</script>
</body>
</html>
<?php
}
?>