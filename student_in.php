<?php
session_name("C_Smart");
session_start();

if (!isset($_SESSION['ss_school_id'])) {
    include 'error_page.php';
    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\" />";
    exit;
}

include "./includes/connect.php";
include "./includes/function.php";
include "./includes/student_pt_options.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>C-Smart | ข้อมูลผู้เรียน (พต.1 / พต.2)</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Google Fonts: Sarabun (รองรับภาษาไทยสวยงาม) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="plugins/AdminLTE4/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./plugins/c-smart-1.0.2.css">

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

        /* ===== Hero Header ===== */
        .student-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .student-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .student-hero::after {
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

        /* ===== Breadcrumb ===== */
        .modern-breadcrumb {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
        }
        .modern-breadcrumb .breadcrumb {
            margin: 0;
        }
        .modern-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .modern-breadcrumb a:hover { text-decoration: underline; }

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
            flex-shrink: 0;
        }
        .section-icon-wrap.bg-personal  { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .section-icon-wrap.bg-application { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
        .section-icon-wrap.bg-interest  { background: linear-gradient(135deg, #f59e0b, #ef4444); }
        .section-icon-wrap.bg-method    { background: linear-gradient(135deg, #14b8a6, #0d9488); }
        .section-icon-wrap.bg-survey    { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .section-icon-wrap.bg-need      { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .section-icon-wrap.bg-goal      { background: linear-gradient(135deg, #10b981, #059669); }
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
            font-size: 0.72rem;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .badge-pt2 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-pt1 { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .section-card-body {
            padding: 1.5rem;
        }

        /* ===== หัวข้อย่อยภายในการ์ด (ตามเลขหัวข้อของแบบฟอร์ม) ===== */
        .form-legend {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin: 0 0 1rem 0;
        }
        .form-legend .legend-num {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.78rem;
            padding: 0.2rem 0.65rem;
            border-radius: 8px;
            white-space: nowrap;
        }
        .form-legend .legend-text {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.95rem;
        }
        .form-legend .legend-hint {
            font-size: 0.78rem;
            color: var(--text-muted);
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

        /* Select2 modernize */
        .select2-container--default .select2-selection--single {
            height: 58px !important;
            border: 1.5px solid var(--border-soft) !important;
            border-radius: 12px !important;
            padding: 1.3rem 1rem 0.4rem 2.4rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: var(--text-dark);
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 56px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
        }

        /* ===== ตัวเลือกแบบ Chip (checkbox/radio) ===== */
        .choice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.5rem;
        }
        .choice-grid.wide { grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); }
        .choice-chip {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
            padding: 0.55rem 0.75rem;
            cursor: pointer;
            font-size: 0.85rem;
            background: white;
            transition: all 0.2s ease;
            user-select: none;
            margin: 0;
        }
        .choice-chip:hover {
            border-color: var(--primary);
            background: #fafbff;
        }
        .choice-chip input {
            accent-color: var(--primary);
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            margin: 0;
        }
        .choice-chip:has(input:checked) {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
            font-weight: 600;
        }
        .choice-chip input:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* กลุ่มย่อยของความต้องการ (2.1 / 2.2 / 2.3) */
        .need-group {
            border: 1px dashed var(--border-soft);
            border-radius: 12px;
            padding: 1.1rem 1.25rem;
            margin-bottom: 1.1rem;
            background: #fcfdff;
        }
        .need-group:last-child { margin-bottom: 0; }
        .need-group-title {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.92rem;
            margin-bottom: 0.75rem;
        }
        .need-group-title .num {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            padding: 0.15rem 0.6rem;
            border-radius: 8px;
            font-size: 0.78rem;
            margin-right: 0.4rem;
        }
        .sub-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0.9rem 0 0.45rem 0;
        }
        .sub-label:first-of-type { margin-top: 0; }

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

        /* ===== Mode badge ===== */
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

        /* Responsive */
        @media (max-width: 768px) {
            .student-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .section-card-body { padding: 1.1rem; }
            .action-bar { flex-direction: column-reverse; }
            .action-bar .btn-modern { width: 100%; justify-content: center; }
            .action-bar-info { width: 100%; text-align: center; margin: 0 0 0.5rem 0; }
            .choice-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .choice-grid { grid-template-columns: 1fr; }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .section-card { animation: fadeInUp 0.4s ease-out; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php
    $edit_id = isset($_REQUEST['edit_id']) ? $_REQUEST['edit_id'] : '';
    $student_id = '';
    $firstname = '';
    $lastname = '';
    $sex_id = '';
    $idcard = '';
    $birthday = '';
    $education_id = '';
    $career_id = '';
    $target_id = '';
    $income = '';
    $address = '';
    $tel = '';
    $nationality_id = '';
    $religion_id = '';
    $register = '';

    // ข้อมูล พต.1 / พต.2 (ค่าเริ่มต้นว่าง)
    $pt = [
        'pt2_no' => '', 'pt2_course_name' => '', 'pt2_course_hours' => '',
        'pt2_interests' => '', 'pt2_interest_other' => '',
        'pt2_statuses' => '', 'pt2_status_other' => '',
        'pt2_learn_method' => '', 'pt2_learn_place' => '', 'pt2_learn_course' => '',
        'pt2_news_source' => '',
        'pt1_has_career' => '', 'pt1_career_detail' => '', 'pt1_certificates' => '',
        'pt1_need_career' => '', 'pt1_need_career_other' => '', 'pt1_need_career_hours' => '', 'pt1_need_career_hours_other' => '',
        'pt1_need_life' => '', 'pt1_need_life_other' => '', 'pt1_need_life_hours' => '', 'pt1_need_life_hours_other' => '',
        'pt1_need_social' => '', 'pt1_need_social_other' => '', 'pt1_need_social_hours' => '', 'pt1_need_social_hours_other' => '',
        'pt1_learn_days' => '', 'pt1_learn_time' => '',
        'pt1_goals' => '', 'pt1_goal_other' => '',
    ];

    if ($edit_id) {
        try {
            $sql = "SELECT * FROM student WHERE student_id = :edit_id AND school_id = :school_id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
            $stmt->bindParam(':school_id', $_SESSION['ss_school_id'], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $student_id = $row['student_id'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $sex_id = $row['sex_id'];
                $idcard = $row['idcard'];
                $birthday = ADYearToBEYear($row['birthday']);
                $education_id = $row['education_id'];
                $career_id = $row['career_id'];
                $target_id = $row['target_id'];
                $income = number_format($row['income']);
                $address = $row['address'];
                $tel = $row['tel'];
                $nationality_id = $row['nationality_id'];
                $religion_id = $row['religion_id'];
                $register = ADYearToBEYear($row['register']);

                // โหลดข้อมูล พต.1 / พต.2 ถ้ามี
                $ptStmt = $pdo->prepare("SELECT * FROM student_pt WHERE student_id = :student_id AND school_id = :school_id LIMIT 1");
                $ptStmt->execute(['student_id' => $student_id, 'school_id' => $_SESSION['ss_school_id']]);
                if ($ptRow = $ptStmt->fetch()) {
                    foreach ($pt as $k => $v) {
                        if (isset($ptRow[$k])) $pt[$k] = (string)$ptRow[$k];
                    }
                }
            } else {
                echo "<meta http-equiv='refresh' content='0;url=index.php'>";
                exit;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    $isEdit = $student_id ? true : false;

    // ตัวช่วย: แปลง CSV เป็น array ของตัวเลือกที่เลือกไว้
    $ptSel = fn($csv) => array_map('intval', array_filter(explode(',', (string)$csv), fn($v) => $v !== ''));

    // ดึงหลักสูตรทั้งหมดในระบบจากฐานข้อมูล (สำหรับช่อง หลักสูตร/กิจกรรม ของ พต.2)
    // หลักสูตรเป็นของระบบกศน. ทั้งระบบ จึงไม่กรองตาม school — ทุกโรงเรียนเห็น dropdown เดียวกัน
    $courseList = [];
    try {
        $cStmt = $pdo->query("SELECT course_id, course_name, course_hour FROM course ORDER BY course_name ASC");
        $courseList = $cStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $courseList = [];
    }

    // โหมดแก้ไข: เทียบชื่อหลักสูตรที่บันทึกไว้กับรายการในฐานข้อมูล
    // ถ้าตรง → เลือก option นั้น, ถ้าไม่ตรง (พิมพ์เองไว้ก่อน) → โหมด "อื่น ๆ"
    // ถ้ามาจากหน้ากิจกรรม (act_id) → เลือกหลักสูตรตามกิจกรรมอัตโนมัติ ไม่ต้องเลือกเอง
    if ($isEdit && isset($_GET['act_id']) && (int)$_GET['act_id'] > 0) {
        $actCourseName = '';
        $actTypeId = 0;
        try {
            $actStmt = $pdo->prepare("SELECT a.course_id, a.type_id, c.culture_id, c.course_name, c.course_hour
                                      FROM activity a
                                      INNER JOIN course c ON c.course_id = a.course_id
                                      WHERE a.activity_id = :act_id AND a.school_id = :school_id
                                      LIMIT 1");
            $actStmt->execute(['act_id' => (int)$_GET['act_id'], 'school_id' => $_SESSION['ss_school_id']]);
            $actCourse = $actStmt->fetch(PDO::FETCH_ASSOC);
            if ($actCourse) {
                $pt['pt2_course_name'] = $actCourse['course_name'];
                $actCourseName = $actCourse['course_name'];
                $actTypeId = (int)$actCourse['type_id'];
                $actCultureId = (int)$actCourse['culture_id'];
                if (!empty($actCourse['course_hour']) && $pt['pt2_course_hours'] === '') {
                    $pt['pt2_course_hours'] = (string)$actCourse['course_hour'];
                }
            }
        } catch (PDOException $e) {
            // ไม่พบกิจกรรม → ใช้ค่าที่บันทึกไว้เดิม
        }
    }
    // กรองกลุ่ม "ความต้องการในการเรียนรู้" (พต.1 ข้อ 2) ตามหมวดกิจกรรม เมื่อมาจากหน้ากิจกรรม
    // อาชีพ → 2.1 | ทักษะชีวิต → 2.2 | พัฒนาสังคมและชุมชน → 2.3 | ไม่ทราบหมวด → แสดงทั้งหมด
    $showNeed = ['career' => true, 'life' => true, 'social' => true];
    if (!empty($actCultureId)) {
        if (in_array($actCultureId, [1, 2, 3, 4, 5, 6, 10])) $only = 'career';
        elseif (in_array($actCultureId, [9, 11])) $only = 'life';
        elseif ($actCultureId == 7) $only = 'social';
        else $only = null;
        if ($only) {
            foreach ($showNeed as $k => $_) $showNeed[$k] = ($k === $only);
            // ถ้าเคยบันทึกข้อมูลในกลุ่มที่ถูกซ่อนไว้ ให้ยังแสดง เพื่อไม่ให้ข้อมูลหายตอนบันทึก
            foreach (['career', 'life', 'social'] as $k) {
                foreach (['', '_other', '_hours', '_hours_other'] as $suf) {
                    if (($pt['pt1_need_' . $k . $suf] ?? '') !== '') $showNeed[$k] = true;
                }
            }
        }
    }
    // พต.1 ข้อ 2 อาชีพปัจจุบัน: ถ้ายังไม่เคยกรอก ให้สอดคล้องกับช่องอาชีพของผู้สมัคร (career_id)
    if ($isEdit && $career_id && $pt['pt1_has_career'] === '' && $pt['pt1_career_detail'] === '') {
        $cnStmt = $pdo->prepare("SELECT career_name FROM career WHERE career_id = :c");
        $cnStmt->execute(['c' => $career_id]);
        $cn = (string)$cnStmt->fetchColumn();
        $pt['pt1_has_career'] = '1';
        if ($cn !== '' && $cn !== 'อื่น ๆ') $pt['pt1_career_detail'] = $cn;
    }
    $ptCourseId = '';
    $ptCourseCustom = '';
    if ($pt['pt2_course_name'] !== '') {
        foreach ($courseList as $c) {
            if ($c['course_name'] === $pt['pt2_course_name']) {
                $ptCourseId = (string)$c['course_id'];
                break;
            }
        }
        if ($ptCourseId === '') {
            $ptCourseId = 'other';
            $ptCourseCustom = $pt['pt2_course_name'];
        }
    }
?>

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item"><a href="student_main.php">ข้อมูลผู้เรียน</a></li>
                    <li class="breadcrumb-item active fw-semibold"><?php echo $isEdit ? "แก้ไขข้อมูลผู้เรียน" : "เพิ่มข้อมูลผู้เรียน"; ?></li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="student-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-<?php echo $isEdit ? 'user-edit' : 'user-plus'; ?>"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-<?php echo $isEdit ? 'pen' : 'plus-circle'; ?>"></i>
                                <?php echo $isEdit ? 'โหมดแก้ไข' : 'โหมดเพิ่มใหม่'; ?>
                            </span>
                            <h1 class="hero-title mt-2"><?php echo $isEdit ? "แก้ไขข้อมูลผู้เรียน" : "เพิ่มข้อมูลผู้เรียน"; ?></h1>
                            <p class="hero-subtitle">ตามแบบ พต.2 (ใบสมัครผู้เรียน) และแบบ พต.1 (แบบสำรวจความต้องการการจัดการเรียนรู้เพื่อการพัฒนาตนเอง)</p>
                        </div>
                    </div>
                    <a href="student_main.php" class="btn-hero-back">
                        <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อ
                    </a>
                </div>
            </div>

            <!-- Form Start -->
            <form class="form" action="student_save.php<?php if($student_id){echo "?submit=EDIT";}?>" method="post" name="form1" id="form1" novalidate>

                <!-- ================================================================ -->
                <!-- แบบ พต.2: ใบสมัครผู้เรียน -->
                <!-- ================================================================ -->

                <!-- การ์ด 1: ข้อมูลใบสมัคร -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-application">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ใบสมัครผู้เรียนหลักสูตรการจัดการเรียนรู้เพื่อการพัฒนาตนเอง</h6>
                            <p class="section-subtitle">ศูนย์ส่งเสริมการเรียนรู้ระดับอำเภอ • กรมส่งเสริมการเรียนรู้ กระทรวงศึกษาธิการ</p>
                        </div>
                        <span class="section-badge badge-pt2">แบบ พต.2</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-hashtag input-icon"></i>
                                    <input type="text" class="form-control" id="pt2_no" name="pt2_no"
                                           autocomplete="off" placeholder=" " value="<?php echo htmlspecialchars($pt['pt2_no']); ?>">
                                    <label for="pt2_no">เลขที่ใบสมัคร</label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating-modern">
                                    <i class="fas fa-book-open input-icon" style="z-index:5;"></i>
                                    <?php if (!empty($courseList)): ?>
                                    <select class="form-select select2" id="pt2_course_select" name="pt2_course_select">
                                        <option value="">-- โปรดเลือกหลักสูตร --</option>
                                        <?php foreach ($courseList as $c):
                                            $sel = ($ptCourseId !== '' && $ptCourseId !== 'other' && (string)$c['course_id'] === $ptCourseId) ? 'selected' : ''; ?>
                                            <option value="<?= $c['course_id'] ?>"
                                                    data-name="<?= htmlspecialchars($c['course_name']) ?>"
                                                    data-hours="<?= (int)$c['course_hour'] ?>" <?= $sel ?>>
                                                <?= htmlspecialchars($c['course_name']) ?> (<?= (int)$c['course_hour'] ?> ชม.)
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="other" <?= ($ptCourseId === 'other') ? 'selected' : '' ?>>อื่น ๆ (พิมพ์เอง)</option>
                                    </select>
                                    <label for="pt2_course_select">หลักสูตร/กิจกรรม</label>
                                    <input type="hidden" id="pt2_course_name" name="pt2_course_name"
                                           value="<?php echo htmlspecialchars($pt['pt2_course_name']); ?>">
                                    <?php else: ?>
                                    <input type="text" class="form-control" id="pt2_course_name" name="pt2_course_name"
                                           autocomplete="off" placeholder=" " value="<?php echo htmlspecialchars($pt['pt2_course_name']); ?>">
                                    <label for="pt2_course_name">หลักสูตร/กิจกรรม</label>
                                    <?php endif; ?>
                                </div>
                                <div class="form-floating-modern mt-2" id="courseCustomBox" style="display: none;">
                                    <i class="fas fa-pen input-icon"></i>
                                    <input type="text" class="form-control" id="pt2_course_custom" name="pt2_course_custom"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($ptCourseCustom); ?>">
                                    <label for="pt2_course_custom">ระบุชื่อหลักสูตร/กิจกรรมเอง</label>
                                </div>
                                <?php if (!empty($courseList)): ?>
                                <small class="text-muted ms-2 mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>เลือกแล้วจำนวนชั่วโมงจะกรอกให้อัตโนมัติ (แก้ไขได้)
                                </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating-modern">
                                    <i class="fas fa-clock input-icon"></i>
                                    <input type="number" min="0" class="form-control" id="pt2_course_hours" name="pt2_course_hours"
                                           autocomplete="off" placeholder=" " value="<?php echo htmlspecialchars($pt['pt2_course_hours']); ?>">
                                    <label for="pt2_course_hours">จำนวน (ชั่วโมง)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 2: หัวข้อ 1 ข้อมูลส่วนตัว -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-personal">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ข้อมูลส่วนตัว</h6>
                            <p class="section-subtitle">กรุณากรอกข้อมูลด้วยตัวบรรจง</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 1</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- ชื่อ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($firstname); ?>">
                                    <label for="firstname">ชื่อ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่ชื่อ</div>
                                </div>
                            </div>
                            <!-- นามสกุล -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($lastname); ?>">
                                    <label for="lastname">นามสกุล <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่นามสกุล</div>
                                </div>
                            </div>
                            <!-- เพศ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-venus-mars input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="sex_id" name="sex_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM sex ORDER BY sex_id ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['sex_id'] == $sex_id) ? 'selected' : '';
                                                echo "<option value='{$row['sex_id']}' {$selected}>{$row['sex_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="sex_id">เพศ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกเพศ</div>
                                </div>
                            </div>
                            <!-- บัตรประชาชน/หนังสือเดินทาง -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" class="form-control" id="idcard" name="idcard"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($idcard); ?>">
                                    <label for="idcard">เลขบัตรประชาชน / หนังสือเดินทาง <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่เลขบัตรประชาชน 13 หลัก หรือเลขหนังสือเดินทาง</div>
                                </div>
                                <small class="text-muted ms-2 mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>กรณีต่างด้าว ใช้เลขหนังสือเดินทาง (passport) พร้อมแนบใบอนุญาตทำงาน
                                </small>
                            </div>
                            <!-- วันเกิด -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-birthday-cake input-icon"></i>
                                    <input type="text" class="form-control datepicker" id="birthday" name="birthday"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($birthday); ?>">
                                    <label for="birthday">เกิดวันที่/เดือน/พ.ศ. <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่วันเกิด</div>
                                </div>
                            </div>
                            <!-- อายุ (คำนวณอัตโนมัติ) -->
                            <div class="col-md-2">
                                <div class="form-floating-modern">
                                    <i class="fas fa-hourglass-half input-icon"></i>
                                    <input type="text" class="form-control" id="ageDisplay" readonly
                                           placeholder=" " style="background: var(--bg-soft);">
                                    <label for="ageDisplay">อายุ (ปี)</label>
                                </div>
                            </div>
                            <!-- สัญชาติ -->
                            <div class="col-md-3">
                                <div class="form-floating-modern">
                                    <i class="fas fa-globe-asia input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="nationality_id" name="nationality_id">
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM nationality ORDER BY nationality_name ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['nationality_id'] == $nationality_id) ? 'selected' : '';
                                                echo "<option value='{$row['nationality_id']}' {$selected}>{$row['nationality_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="nationality_id">สัญชาติ</label>
                                </div>
                            </div>
                            <!-- ศาสนา -->
                            <div class="col-md-3">
                                <div class="form-floating-modern">
                                    <i class="fas fa-praying-hands input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="religion_id" name="religion_id">
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM religion ORDER BY religion_id ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['religion_id'] == $religion_id) ? 'selected' : '';
                                                echo "<option value='{$row['religion_id']}' {$selected}>{$row['religion_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="religion_id">ศาสนา</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 3: การศึกษา อาชีพ กลุ่มเป้าหมาย -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-academic" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ระดับการศึกษา อาชีพ และกลุ่มเป้าหมาย</h6>
                            <p class="section-subtitle">ตามแบบ พต.2 หัวข้อ 1 (ระดับการศึกษา) หัวข้อ 4 (อาชีพผู้สมัคร) และหัวข้อ 5 (กลุ่มเป้าหมาย)</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 4-5</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <!-- ระดับการศึกษา -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-graduation-cap input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="education_id" name="education_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM education ORDER BY education_id ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['education_id'] == $education_id) ? 'selected' : '';
                                                echo "<option value='{$row['education_id']}' {$selected}>{$row['education_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="education_id">ระดับการศึกษา <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกระดับการศึกษา</div>
                                </div>
                            </div>
                            <!-- อาชีพของผู้สมัคร -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-briefcase input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="career_id" name="career_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM career ORDER BY career_id ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['career_id'] == $career_id) ? 'selected' : '';
                                                echo "<option value='{$row['career_id']}' {$selected}>{$row['career_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="career_id">อาชีพของผู้สมัคร <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกอาชีพ</div>
                                </div>
                            </div>
                            <!-- กลุ่มเป้าหมาย -->
                            <div class="col-md-4">
                                <div class="form-floating-modern">
                                    <i class="fas fa-bullseye input-icon" style="z-index:5;"></i>
                                    <select class="form-select select2" id="target_id" name="target_id" required>
                                        <option value="" selected disabled>-- โปรดเลือก --</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT * FROM target ORDER BY target_id ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($row['target_id'] == $target_id) ? 'selected' : '';
                                                echo "<option value='{$row['target_id']}' {$selected}>{$row['target_name']}</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </select>
                                    <label for="target_id">กลุ่มเป้าหมาย <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาเลือกกลุ่มเป้าหมาย</div>
                                </div>
                            </div>
                            <!-- รายได้ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-money-bill-wave input-icon"></i>
                                    <input type="text" class="form-control" id="income" name="income"
                                           autocomplete="off" required maxlength="11" placeholder=" "
                                           value="<?php echo htmlspecialchars($income); ?>">
                                    <label for="income">รายได้ต่อปี (บาท) <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่รายได้ (กรณีไม่มีรายได้ ใส่ 0)</div>
                                </div>
                            </div>
                            <!-- ที่อยู่ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-home input-icon"></i>
                                    <input type="text" class="form-control" id="address" name="address"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($address); ?>">
                                    <label for="address">ที่อยู่ปัจจุบัน (บ้านเลขที่/หมู่/ตำบล...) <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่ที่อยู่</div>
                                </div>
                            </div>
                            <!-- โทรศัพท์ -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" class="form-control" id="tel" name="tel"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($tel); ?>">
                                    <label for="tel">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่เบอร์โทรศัพท์</div>
                                </div>
                            </div>
                            <!-- วันที่สมัคร -->
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <input type="text" class="form-control datepicker" id="register" name="register"
                                           autocomplete="off" required placeholder=" "
                                           value="<?php echo htmlspecialchars($register); ?>">
                                    <label for="register">วันที่/เดือน/ปี ที่สมัคร (พ.ศ.) <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">กรุณาใส่วันที่สมัคร</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 4: หัวข้อ 2 สนใจเข้าร่วมกิจกรรม -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-interest">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <h6 class="section-title">สนใจเข้าร่วมกิจกรรม เนื่องจาก</h6>
                            <p class="section-subtitle">เลือกได้มากกว่า 1 ข้อ</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 2</span>
                    </div>
                    <div class="section-card-body">
                        <div class="choice-grid wide">
                            <?php foreach ($PT2_INTERESTS as $code => $label):
                                $checked = in_array($code, $ptSel($pt['pt2_interests']), true) ? 'checked' : ''; ?>
                                <label class="choice-chip">
                                    <input type="checkbox" name="pt2_interests[]" value="<?= $code ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-floating-modern mt-2">
                                    <i class="fas fa-pen input-icon"></i>
                                    <input type="text" class="form-control" id="pt2_interest_other" name="pt2_interest_other"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt2_interest_other']); ?>">
                                    <label for="pt2_interest_other">อื่น ๆ (โปรดระบุ)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 5: หัวข้อ 3 สถานะของผู้สมัคร -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-interest" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <h6 class="section-title">สถานะของผู้สมัคร</h6>
                            <p class="section-subtitle">เลือกได้มากกว่า 1 ข้อ</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 3</span>
                    </div>
                    <div class="section-card-body">
                        <div class="choice-grid wide">
                            <?php foreach ($PT2_STATUSES as $code => $label):
                                $checked = in_array($code, $ptSel($pt['pt2_statuses']), true) ? 'checked' : ''; ?>
                                <label class="choice-chip">
                                    <input type="checkbox" name="pt2_statuses[]" value="<?= $code ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-floating-modern mt-2">
                                    <i class="fas fa-pen input-icon"></i>
                                    <input type="text" class="form-control" id="pt2_status_other" name="pt2_status_other"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt2_status_other']); ?>">
                                    <label for="pt2_status_other">อื่น ๆ (โปรดระบุ)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 6: หัวข้อ 6 วิธีการเรียนรู้ -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-method">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <h6 class="section-title">วิธีการเรียนรู้</h6>
                            <p class="section-subtitle">เลือกวิธีการเรียนรู้ที่ต้องการ</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 6</span>
                    </div>
                    <div class="section-card-body">
                        <div class="choice-grid">
                            <?php foreach ($PT2_LEARN_METHODS as $code => $label):
                                $checked = ($pt['pt2_learn_method'] == $code) ? 'checked' : ''; ?>
                                <label class="choice-chip">
                                    <input type="radio" name="pt2_learn_method" value="<?= $code ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- ส่วนกรอกเมื่อเลือก "แบบรายบุคคล" -->
                        <div id="learnIndividualBox" style="display: none;">
                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <div class="form-floating-modern mt-2">
                                        <i class="fas fa-location-dot input-icon"></i>
                                        <input type="text" class="form-control" id="pt2_learn_place" name="pt2_learn_place"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt2_learn_place']); ?>">
                                        <label for="pt2_learn_place">สถานที่/ช่องทางการเรียนรู้ของตน</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating-modern mt-2">
                                        <i class="fas fa-book input-icon"></i>
                                        <input type="text" class="form-control" id="pt2_learn_course" name="pt2_learn_course"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt2_learn_course']); ?>">
                                        <label for="pt2_learn_course">ชื่อหลักสูตร</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 7: หัวข้อ 7 ข่าวสารการรับสมัคร -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-method" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ท่านได้รับข่าวสารการรับสมัครจาก</h6>
                            <p class="section-subtitle">เช่น ป้ายประกาศ กลุ่มไลน์ เว็บไซต์ ผู้นำชุมชน ฯลฯ</p>
                        </div>
                        <span class="section-badge badge-pt2">พต.2 หัวข้อ 7</span>
                    </div>
                    <div class="section-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="fas fa-envelope-open-text input-icon"></i>
                                    <input type="text" class="form-control" id="pt2_news_source" name="pt2_news_source"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt2_news_source']); ?>">
                                    <label for="pt2_news_source">แหล่งข่าวสารการรับสมัคร</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================================================================ -->
                <!-- แบบ พต.1: แบบสำรวจความต้องการการจัดการเรียนรู้ -->
                <!-- ================================================================ -->

                <!-- การ์ด 8: พต.1 ส่วนที่ 1 ข้อมูลส่วนตัว (อาชีพปัจจุบัน + วุฒิบัตร) -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-survey">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h6 class="section-title">แบบสำรวจความต้องการการจัดการเรียนรู้ เพื่อการพัฒนาตนเอง สำหรับประชาชน — ข้อมูลส่วนตัว</h6>
                            <p class="section-subtitle">อาชีพปัจจุบัน และวุฒิบัตร/เกียรติบัตร/ประกาศนียบัตร</p>
                        </div>
                        <span class="section-badge badge-pt1">แบบ พต.1 ส่วนที่ 1</span>
                    </div>
                    <div class="section-card-body">
                        <div class="form-legend">
                            <span class="legend-num">ข้อ 2</span>
                            <span class="legend-text">อาชีพปัจจุบัน</span>
                        </div>
                        <div class="choice-grid">
                            <label class="choice-chip">
                                <input type="radio" name="pt1_has_career" value="1" <?= ($pt['pt1_has_career'] === '1') ? 'checked' : '' ?>>
                                <span>มีอาชีพ</span>
                            </label>
                            <label class="choice-chip">
                                <input type="radio" name="pt1_has_career" value="0" <?= ($pt['pt1_has_career'] === '0') ? 'checked' : '' ?>>
                                <span>ไม่มีอาชีพ</span>
                            </label>
                        </div>
                        <div class="row g-3 mt-0" id="careerDetailBox" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-floating-modern mt-2">
                                    <i class="fas fa-briefcase input-icon"></i>
                                    <input type="text" class="form-control" id="pt1_career_detail" name="pt1_career_detail"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt1_career_detail']); ?>">
                                    <label for="pt1_career_detail">ระบุอาชีพ</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="form-legend">
                            <span class="legend-num">ข้อ 4</span>
                            <span class="legend-text">วุฒิบัตร / เกียรติบัตร / ประกาศนียบัตร</span>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="fas fa-certificate input-icon"></i>
                                    <input type="text" class="form-control" id="pt1_certificates" name="pt1_certificates"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt1_certificates']); ?>">
                                    <label for="pt1_certificates">วุฒิบัตร/เกียรติบัตร/ประกาศนียบัตรที่ได้รับ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- การ์ด 9: พต.1 ส่วนที่ 2 ความต้องการในการเรียนรู้ -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-need">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div>
                            <h6 class="section-title">ความต้องการในการเรียนรู้</h6>
                            <p class="section-subtitle">เลือกหัวข้อที่สนใจได้มากกว่า 1 ข้อ พร้อมระบุจำนวนชั่วโมงที่ต้องการ</p>
                        </div>
                        <span class="section-badge badge-pt1">พต.1 ส่วนที่ 2</span>
                    </div>
                    <div class="section-card-body">

                        <!-- 2.1 ด้านการประกอบและพัฒนาอาชีพ -->
                        <?php if ($showNeed['career']): ?>
                        <div class="need-group">
                            <div class="need-group-title"><span class="num">2.1</span>ด้านการประกอบและพัฒนาอาชีพ</div>
                            <div class="choice-grid">
                                <?php foreach ($PT1_NEED_CAREER as $code => $label):
                                    $checked = in_array($code, $ptSel($pt['pt1_need_career']), true) ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="checkbox" name="pt1_need_career[]" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            // ถ้ามาจากกิจกรรม (act_id) และชื่อหลักสูตร/โครงการไม่ตรงตัวเลือก 2.1 → กรอกในช่อง "อื่น ๆ" อัตโนมัติ
                            // เงื่อนไข: กิจกรรมต้องเป็นประเภท 1 = โครงการพัฒนาอาชีพชุมชน เท่านั้น
                            $careerOtherValue = $pt['pt1_need_career_other'];
                            if ($careerOtherValue === '' && $actCourseName !== '' && $actTypeId === 1
                                && !in_array($actCourseName, $PT1_NEED_CAREER, true)) {
                                $careerOtherValue = $actCourseName;
                            }
                            ?>
                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <div class="form-floating-modern mt-2">
                                        <i class="fas fa-pen input-icon"></i>
                                        <input type="text" class="form-control" id="pt1_need_career_other" name="pt1_need_career_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($careerOtherValue); ?>">
                                        <label for="pt1_need_career_other">อื่น ๆ (โปรดระบุ)</label>
                                    </div>
                                </div>
                            </div>
                            <p class="sub-label">จำนวนชั่วโมงที่ต้องการเรียน</p>
                            <div class="choice-grid">
                                <?php foreach ($PT1_HOUR_OPTIONS as $code => $label):
                                    $checked = ($pt['pt1_need_career_hours'] == $code && $pt['pt1_need_career_hours'] !== '') ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="radio" name="pt1_need_career_hours" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-md-4">
                                    <div class="form-floating-modern mt-2 hours-other-box" id="box_pt1_need_career_hours_other" style="display: none;">
                                        <i class="fas fa-clock input-icon"></i>
                                        <input type="number" min="0" class="form-control" id="pt1_need_career_hours_other" name="pt1_need_career_hours_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt1_need_career_hours_other']); ?>">
                                        <label for="pt1_need_career_hours_other">ระบุจำนวนชั่วโมง</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 2.2 ด้านทักษะชีวิต -->
                        <?php if ($showNeed['life']): ?>
                        <div class="need-group">
                            <div class="need-group-title"><span class="num">2.2</span>ด้านทักษะชีวิต</div>
                            <div class="choice-grid wide">
                                <?php foreach ($PT1_NEED_LIFE as $code => $label):
                                    $checked = in_array($code, $ptSel($pt['pt1_need_life']), true) ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="checkbox" name="pt1_need_life[]" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <div class="form-floating-modern mt-2">
                                        <i class="fas fa-pen input-icon"></i>
                                        <input type="text" class="form-control" id="pt1_need_life_other" name="pt1_need_life_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt1_need_life_other']); ?>">
                                        <label for="pt1_need_life_other">อื่น ๆ (โปรดระบุ)</label>
                                    </div>
                                </div>
                            </div>
                            <p class="sub-label">จำนวนชั่วโมงที่ต้องการเรียน</p>
                            <div class="choice-grid">
                                <?php foreach ($PT1_HOUR_OPTIONS as $code => $label):
                                    $checked = ($pt['pt1_need_life_hours'] == $code && $pt['pt1_need_life_hours'] !== '') ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="radio" name="pt1_need_life_hours" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-md-4">
                                    <div class="form-floating-modern mt-2 hours-other-box" id="box_pt1_need_life_hours_other" style="display: none;">
                                        <i class="fas fa-clock input-icon"></i>
                                        <input type="number" min="0" class="form-control" id="pt1_need_life_hours_other" name="pt1_need_life_hours_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt1_need_life_hours_other']); ?>">
                                        <label for="pt1_need_life_hours_other">ระบุจำนวนชั่วโมง</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 2.3 ด้านพัฒนาสังคมและชุมชน -->
                        <?php if ($showNeed['social']): ?>
                        <div class="need-group">
                            <div class="need-group-title"><span class="num">2.3</span>ด้านพัฒนาสังคมและชุมชน</div>
                            <div class="choice-grid wide">
                                <?php foreach ($PT1_NEED_SOCIAL as $code => $label):
                                    $checked = in_array($code, $ptSel($pt['pt1_need_social']), true) ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="checkbox" name="pt1_need_social[]" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <div class="form-floating-modern mt-2">
                                        <i class="fas fa-pen input-icon"></i>
                                        <input type="text" class="form-control" id="pt1_need_social_other" name="pt1_need_social_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt1_need_social_other']); ?>">
                                        <label for="pt1_need_social_other">อื่น ๆ (โปรดระบุ)</label>
                                    </div>
                                </div>
                            </div>
                            <p class="sub-label">จำนวนชั่วโมงที่ต้องการเรียน</p>
                            <div class="choice-grid">
                                <?php foreach ($PT1_HOUR_OPTIONS as $code => $label):
                                    $checked = ($pt['pt1_need_social_hours'] == $code && $pt['pt1_need_social_hours'] !== '') ? 'checked' : ''; ?>
                                    <label class="choice-chip">
                                        <input type="radio" name="pt1_need_social_hours" value="<?= $code ?>" <?= $checked ?>>
                                        <span><?= htmlspecialchars($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-3 mt-0">
                                <div class="col-md-4">
                                    <div class="form-floating-modern mt-2 hours-other-box" id="box_pt1_need_social_hours_other" style="display: none;">
                                        <i class="fas fa-clock input-icon"></i>
                                        <input type="number" min="0" class="form-control" id="pt1_need_social_hours_other" name="pt1_need_social_hours_other"
                                               autocomplete="off" placeholder=" "
                                               value="<?php echo htmlspecialchars($pt['pt1_need_social_hours_other']); ?>">
                                        <label for="pt1_need_social_hours_other">ระบุจำนวนชั่วโมง</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- การ์ด 10: พต.1 ส่วนที่ 2.4 + ส่วนที่ 3 -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-icon-wrap bg-goal">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div>
                            <h6 class="section-title">วันเวลาที่สะดวกเรียน และจุดมุ่งหมายในการเรียนรู้</h6>
                            <p class="section-subtitle">จุดมุ่งหมายเลือกได้มากกว่า 1 ข้อ</p>
                        </div>
                        <span class="section-badge badge-pt1">พต.1 ส่วนที่ 2.4 และ 3</span>
                    </div>
                    <div class="section-card-body">
                        <div class="form-legend">
                            <span class="legend-num">ข้อ 2.4</span>
                            <span class="legend-text">วันและเวลาที่สามารถมาเรียน</span>
                        </div>
                        <div class="choice-grid">
                            <?php foreach ($PT1_LEARN_DAYS as $code => $label):
                                $checked = in_array($code, $ptSel($pt['pt1_learn_days']), true) ? 'checked' : ''; ?>
                                <label class="choice-chip">
                                    <input type="checkbox" name="pt1_learn_days[]" value="<?= $code ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="row g-3 mt-0">
                            <?php
                            $learnStart = $learnEnd = '';
                            if (preg_match('/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', (string)$pt['pt1_learn_time'], $lm)) {
                                [$learnStart, $learnEnd] = [$lm[1], $lm[2]];
                            }
                            $timeOpts = [];
                            for ($m = 6 * 60; $m <= 21 * 60; $m += 30) {
                                $timeOpts[] = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
                            }
                            ?>
                            <div class="col-md-3 col-6">
                                <label class="form-label mt-2 mb-1" for="pt1_learn_start"><i class="fas fa-clock"></i> เวลาเริ่ม</label>
                                <select class="form-select" id="pt1_learn_start" name="pt1_learn_start">
                                    <option value="">-- เลือกเวลาเริ่ม --</option>
                                    <?php foreach ($timeOpts as $t): ?>
                                        <option value="<?= $t ?>" <?= $t === $learnStart ? 'selected' : '' ?>><?= $t ?> น.</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label mt-2 mb-1" for="pt1_learn_end"><i class="fas fa-clock"></i> เวลาสิ้นสุด</label>
                                <select class="form-select" id="pt1_learn_end" name="pt1_learn_end">
                                    <option value="">-- เลือกเวลาสิ้นสุด --</option>
                                    <?php foreach ($timeOpts as $t): ?>
                                        <option value="<?= $t ?>" <?= $t === $learnEnd ? 'selected' : '' ?>><?= $t ?> น.</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="form-legend">
                            <span class="legend-num">ส่วนที่ 3</span>
                            <span class="legend-text">จุดมุ่งหมายในการเรียนรู้</span>
                            <span class="legend-hint">(เลือกได้มากกว่า 1 ข้อ)</span>
                        </div>
                        <div class="choice-grid wide">
                            <?php foreach ($PT1_GOALS as $code => $label):
                                $checked = in_array($code, $ptSel($pt['pt1_goals']), true) ? 'checked' : ''; ?>
                                <label class="choice-chip">
                                    <input type="checkbox" name="pt1_goals[]" value="<?= $code ?>" <?= $checked ?>>
                                    <span><?= htmlspecialchars($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-floating-modern mt-2">
                                    <i class="fas fa-pen input-icon"></i>
                                    <input type="text" class="form-control" id="pt1_goal_other" name="pt1_goal_other"
                                           autocomplete="off" placeholder=" "
                                           value="<?php echo htmlspecialchars($pt['pt1_goal_other']); ?>">
                                    <label for="pt1_goal_other">อื่น ๆ (โปรดระบุ)</label>
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
                    <a href="student_main.php" class="btn btn-modern btn-outline-modern">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-modern btn-primary-grad">
                        <i class="fas fa-save"></i>
                        <?php echo $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล'; ?>
                    </button>
                    <input type="hidden" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                </div>

            </form>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script src="plugins/AdminLTE4/dist/js/adminlte.min.js"></script>

<script type="text/javascript">
    function ADYearToBEYear(dateString) {
        if (!dateString) return '';
        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString;
        let year = parseInt(parts[2]);
        if (year < 2400) year += 543;
        return `${parts[0]}/${parts[1]}/${year}`;
    }

    function BEYearToADYear(dateString) {
        if (!dateString) return '';
        let parts = dateString.split('/');
        if (parts.length !== 3) return dateString;
        let year = parseInt(parts[2]);
        if (year > 2400) year -= 543;
        return `${parts[0]}/${parts[1]}/${year}`;
    }

    // คำนวณอายุจากวันเกิด (พ.ศ.)
    function updateAge() {
        const val = $('#birthday').val();
        const box = $('#ageDisplay');
        if (!val) { box.val(''); return; }
        const parts = BEYearToADYear(val).split('/');
        if (parts.length !== 3) { box.val(''); return; }
        const b = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
        if (isNaN(b.getTime())) { box.val(''); return; }
        const today = new Date();
        let age = today.getFullYear() - b.getFullYear();
        const m = today.getMonth() - b.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < b.getDate())) age--;
        box.val(age >= 0 && age < 150 ? age : '');
    }

    // ซิงก์ช่องหลักสูตร/กิจกรรม (ดึงจากฐานข้อมูล) → hidden pt2_course_name + เติมชั่วโมงอัตโนมัติ
    function syncCourse(autofillHours) {
        const sel = $('#pt2_course_select');
        if (!sel.length) return;
        const v = sel.val();
        if (v === 'other') {
            $('#courseCustomBox').show();
            $('#pt2_course_name').val($('#pt2_course_custom').val());
        } else if (v) {
            $('#courseCustomBox').hide();
            const opt = sel.find('option:selected');
            $('#pt2_course_name').val(opt.data('name') || '');
            if (autofillHours && opt.data('hours') !== undefined && opt.data('hours') !== '') {
                $('#pt2_course_hours').val(opt.data('hours'));
            }
        } else {
            $('#courseCustomBox').hide();
            $('#pt2_course_name').val('');
        }
    }
    // แสดง/ซ่อนกล่องตามเงื่อนไข
    function toggleCareerDetail() {
        const val = $('input[name="pt1_has_career"]:checked').val();
        $('#careerDetailBox').toggle(val === '1');
    }
    function toggleLearnIndividual() {
        const val = $('input[name="pt2_learn_method"]:checked').val();
        $('#learnIndividualBox').toggle(val === '2');
    }
    function toggleHoursOther(radioName, boxId) {
        const val = $(`input[name="${radioName}"]:checked`).val();
        $('#' + boxId).toggle(val === '3');
    }
    function toggleAllHoursOthers() {
        toggleHoursOther('pt1_need_career_hours', 'box_pt1_need_career_hours_other');
        toggleHoursOther('pt1_need_life_hours', 'box_pt1_need_life_hours_other');
        toggleHoursOther('pt1_need_social_hours', 'box_pt1_need_social_hours_other');
    }

    $(document).ready(function() {

        // Select2 init with theme matching
        $('.select2').select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });

        // Datepicker (ภาษาไทย พ.ศ.)
        var datepickerElements = $('.datepicker');
        if (datepickerElements.length > 0) {
            $.datepicker.regional['th'] = {
                closeText: 'ปิด',
                prevText: '< ย้อน',
                nextText: 'ถัดไป >',
                currentText: 'วันนี้',
                monthNames: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
                monthNamesShort: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.',
                'ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
                dayNames: ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'],
                dayNamesShort: ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],
                dayNamesMin: ['อา','จ','อ','พ','พฤ','ศ','ส'],
                weekHeader: 'Wk',
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: '',
                beforeShow: function() {
                    setTimeout(function(){
                        $.each($(".ui-datepicker-year option"), function(j, k) {
                            var textYear = parseInt($(k).text()) + 543;
                            $(k).text(textYear);
                        });
                    }, 50);
                }
            };
            $.datepicker.setDefaults($.datepicker.regional['th']);

            datepickerElements.datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+1',
                onSelect: function(dateText) {
                    $(this).val(ADYearToBEYear(dateText));
                    updateAge();
                },
                onChangeMonthYear: function() {
                    setTimeout(function(){
                        $.each($(".ui-datepicker-year option"), function(j, k) {
                            var textYear = parseInt($(k).text()) + 543;
                            $(k).text(textYear);
                        });
                    }, 50);
                }
            });
        }

        // Cleave formatters
        new Cleave('#idcard', {
            numericOnly: true,
            delimiter: '-',
            blocks: [1, 4, 5, 2, 1]
        });

        new Cleave('#birthday', {
            date: true,
            delimiter: '/',
            datePattern: ['d', 'm', 'Y'],
            onValueChanged: function() { updateAge(); }
        });

        new Cleave('#register', {
            date: true,
            delimiter: '/',
            datePattern: ['d', 'm', 'Y']
        });

        new Cleave('#income', {
            numeral: true,
            numeralPositiveOnly: true,
            numeralThousandsGroupStyle: 'thousand',
            rawValueTrimPrefix: true
        });

        new Cleave('#tel', {
            numericOnly: true,
            delimiter: '-',
            blocks: [3, 7]
        });

        // Toggle ช่องหลักสูตรจากฐานข้อมูล
        $('#pt2_course_select').on('change', function() { syncCourse(true); });
        $('#pt2_course_custom').on('input', function() {
            if ($('#pt2_course_select').val() === 'other') {
                $('#pt2_course_name').val($(this).val());
            }
        });

        // Toggle เงื่อนไขต่างๆ
        $('input[name="pt1_has_career"]').on('change', toggleCareerDetail);

        // อาชีพปัจจุบัน (พต.1 ข้อ 2) ตามช่องอาชีพของผู้สมัคร
        let lastAutoCareer = $('#career_id option:selected').text().trim();
        $('#career_id').on('change', function() {
            const name = $(this).find('option:selected').text().trim();
            const $detail = $('#pt1_career_detail');
            $('input[name="pt1_has_career"][value="1"]').prop('checked', true);
            if ($detail.val() === '' || $detail.val() === lastAutoCareer) {
                $detail.val(name === 'อื่น ๆ' ? '' : name);
            }
            lastAutoCareer = name === 'อื่น ๆ' ? '' : name;
            toggleCareerDetail();
        });
        $('input[name="pt2_learn_method"]').on('change', toggleLearnIndividual);
        $('input[name="pt1_need_career_hours"], input[name="pt1_need_life_hours"], input[name="pt1_need_social_hours"]').on('change', toggleAllHoursOthers);

        toggleCareerDetail();
        toggleLearnIndividual();
        toggleAllHoursOthers();
        syncCourse(false);
        updateAge();

        // Bootstrap 5 Form Validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.form');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');

                    const idcard = document.getElementById('idcard');
                    const id = idcard.value.replace(/(_)|(-)/g, '');

                    if (id.length === 13) {
                        let sum = 0;
                        for (let i = 0; i < 12; i++) {
                            sum += parseFloat(id.charAt(i)) * (13 - i);
                        }
                        if ((11 - sum % 11) % 10 !== parseFloat(id.charAt(12))) {
                            idcard.setCustomValidity("เลขบัตรประชาชนไม่ถูกต้อง");
                            form.classList.add('was-validated');
                            event.preventDefault();
                            event.stopPropagation();
                            return;
                        } else {
                            idcard.setCustomValidity("");
                            $.ajax({
                                type: "POST",
                                url: "./ajax/student_idcard_check.php",
                                data: { student_id: $('#student_id').val(), idcard: $('#idcard').val() },
                                async: false,
                                success: function(result) {
                                    if (result == "false") {
                                        idcard.setCustomValidity("เลขบัตรประชาชนซ้ำ");
                                        form.classList.add('was-validated');
                                        event.preventDefault();
                                        event.stopPropagation();
                                        return;
                                    }
                                }
                            });
                        }
                    } else if (/^[A-Za-z0-9]{6,20}$/.test(id)) {
                        // หนังสือเดินทาง (passport) — ตรวจซ้ำเท่านั้น
                        idcard.setCustomValidity("");
                        $.ajax({
                            type: "POST",
                            url: "./ajax/student_idcard_check.php",
                            data: { student_id: $('#student_id').val(), idcard: $('#idcard').val() },
                            async: false,
                            success: function(result) {
                                if (result == "false") {
                                    idcard.setCustomValidity("เลขนี้มีในระบบแล้ว");
                                    form.classList.add('was-validated');
                                    event.preventDefault();
                                    event.stopPropagation();
                                    return;
                                }
                            }
                        });
                    } else {
                        idcard.setCustomValidity("กรุณาใส่เลขบัตรประชาชน 13 หลัก หรือเลขหนังสือเดินทาง");
                        form.classList.add('was-validated');
                        event.preventDefault();
                        event.stopPropagation();
                        return;
                    }
                }, false);
            });
        })();
    });
</script>
</body>
</html>
