<?php
// course_main.php - Modern Design (Guest Accessible)
session_name("C_Smart");
session_start();

include "./includes/connect.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง | C-Smart</title>

    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
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
        .course-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
        }
        .course-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .course-hero::after {
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
        .btn-hero-view {
            background: white;
            color: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-hero-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: var(--primary-dark);
        }

        /* ===== Summary Bar ===== */
        .summary-bar {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-soft);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .summary-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }
        .summary-icon.green {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .summary-icon.orange {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
        }
        .summary-label {
            color: var(--text-muted);
            font-size: 0.825rem;
            margin: 0;
        }
        .summary-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .summary-value small {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }
        .summary-divider {
            width: 1px;
            height: 50px;
            background: var(--border-soft);
        }

        /* ===== Section Title ===== */
        .section-title-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .section-title-modern {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .section-title-modern::before {
            content: '';
            width: 4px;
            height: 22px;
            background: linear-gradient(180deg, #4f46e5, #ec4899);
            border-radius: 4px;
        }
        .section-subtitle-modern {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        /* ===== Curriculum Cards (3 หลักสูตร) ===== */
        .curriculum-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
        }
        .curriculum-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        /* ส่วนหัวการ์ด (สีตามหลักสูตร) */
        .curriculum-top {
            color: white;
            padding: 2rem 1.5rem 1.6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .curriculum-top::before {
            content: '';
            position: absolute;
            top: -55%;
            right: -12%;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            border-radius: 50%;
        }
        .curriculum-top::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: -12%;
            width: 190px;
            height: 190px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* ธีมสีของแต่ละหลักสูตร */
        .theme-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .theme-indigo { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
        .theme-cyan   { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

        .curriculum-top .inner {
            position: relative;
            z-index: 2;
        }
        .curriculum-icon {
            width: 76px;
            height: 76px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 0.9rem;
            transition: all 0.3s ease;
        }
        .curriculum-card:hover .curriculum-icon {
            transform: scale(1.08) rotate(-5deg);
        }
        .curriculum-num {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: rgba(255,255,255,0.22);
            border: 1px solid rgba(255,255,255,0.35);
            padding: 0.18rem 0.8rem;
            border-radius: 999px;
        }
        .curriculum-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0.45rem 0 0.35rem 0;
            line-height: 1.35;
            letter-spacing: -0.3px;
        }
        .curriculum-desc {
            font-size: 0.85rem;
            opacity: 0.92;
            margin: 0;
        }

        /* ส่วนเนื้อหาการ์ด */
        .curriculum-body {
            padding: 1.4rem 1.5rem 1.4rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .curriculum-stats {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-bottom: 1.15rem;
        }
        .curriculum-stat {
            text-align: center;
            flex: 1;
        }
        .curriculum-stat .val {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.15;
            letter-spacing: -0.5px;
        }
        .curriculum-stat .lbl {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 0.15rem;
        }
        .curriculum-stat-divider {
            width: 1px;
            height: 42px;
            background: var(--border-soft);
        }

        /* สัดส่วนต่อทั้งหมด */
        .curriculum-progress {
            height: 7px;
            background: var(--bg-soft);
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 0.45rem;
        }
        .curriculum-progress-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 0.8s ease;
        }
        .bar-green  { background: linear-gradient(90deg, #10b981, #059669); }
        .bar-indigo { background: linear-gradient(90deg, #6366f1, #4338ca); }
        .bar-cyan   { background: linear-gradient(90deg, #06b6d4, #0891b2); }

        .progress-text {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 1.1rem;
            font-weight: 500;
        }

        /* ปุ่มดูรายการหลักสูตร */
        .btn-curriculum {
            margin-top: auto;
            width: 100%;
            padding: 0.75rem;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }
        .btn-curriculum:hover {
            transform: translateY(-2px);
            color: white;
            filter: brightness(1.08);
            box-shadow: 0 8px 22px rgba(0,0,0,0.15);
        }
        .btn-curriculum i { transition: transform 0.25s ease; }
        .btn-curriculum:hover i { transform: translateX(3px); }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .curriculum-card { animation: fadeInUp 0.5s ease-out backwards; }
        .curriculum-card.delay-1 { animation-delay: 0.05s; }
        .curriculum-card.delay-2 { animation-delay: 0.15s; }
        .curriculum-card.delay-3 { animation-delay: 0.25s; }

        /* Responsive */
        @media (max-width: 768px) {
            .course-hero { padding: 1.5rem; }
            .hero-title { font-size: 1.25rem; }
            .hero-avatar { width: 56px; height: 56px; font-size: 1.5rem; }
            .summary-divider { display: none; }
            .summary-bar { flex-direction: column; align-items: flex-start; }
            .curriculum-top { padding: 1.5rem 1.25rem 1.3rem; }
            .curriculum-title { font-size: 1.15rem; }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-bs-theme="light">

<?php include "./modules/header.inc.php"; ?>
<?php include "./sidebar.php"; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">

            <?php
            // สถิติรวมของทั้งระบบ
            $totalCourses = 0;
            try {
                $totalStmt = $pdo->query("SELECT COUNT(*) AS c, COALESCE(SUM(course_hour),0) AS h FROM course");
                $totalData = $totalStmt->fetch(PDO::FETCH_ASSOC);
                $totalCourses = (int)($totalData['c'] ?? 0);
            } catch (PDOException $e) {}

            // แยกหมวดตามประเภทกิจกรรมที่เลือกตอนจัดตั้งกลุ่ม นับหลักสูตรเดียวกันในหมวดเดียวเพียงครั้งเดียว
            $curriculums = [];
            try {
                $stmt = $pdo->query("SELECT c.culture_id, c.culture_name, c.culture_objective,
                                            COUNT(co.course_id) AS course_count,
                                            COALESCE(SUM(co.course_hour), 0) AS total_hours
                                     FROM culture c
                                     LEFT JOIN course co ON co.culture_id = c.culture_id
                                     GROUP BY c.culture_id, c.culture_name, c.culture_objective
                                     ORDER BY c.culture_id ASC");
                $curriculums = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $curriculums = [];
            }

            $curriculumCount = count($curriculums);

            // สีและไอคอนของการ์ด วนตามลำดับแถวในตาราง culture
            $cardPalette = [
                ['theme' => 'green',  'icon' => 'fas fa-users'],
                ['theme' => 'indigo', 'icon' => 'fas fa-certificate'],
                ['theme' => 'cyan',   'icon' => 'fas fa-microchip'],
                ['theme' => 'green',  'icon' => 'fas fa-people-group'],
                ['theme' => 'indigo', 'icon' => 'fas fa-seedling'],
                ['theme' => 'cyan',   'icon' => 'fas fa-heart-pulse'],
                ['theme' => 'green',  'icon' => 'fas fa-hand-holding-heart'],
            ];

            // หาหมวดที่มีหลักสูตรมากที่สุด
            $maxGroupCount = 0;
            $topCurriculum = null;
            foreach ($curriculums as $c) {
                if ((int)$c['course_count'] > $maxGroupCount) {
                    $maxGroupCount = (int)$c['course_count'];
                    $topCurriculum = $c;
                }
            }
            ?>

            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>หน้าหลัก</a></li>
                    <li class="breadcrumb-item active fw-semibold">หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง</li>
                </ol>
            </nav>

            <!-- Hero Header -->
            <div class="course-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-avatar">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div>
                            <span class="mode-badge">
                                <i class="fas fa-layer-group"></i>
                                ระบบจัดการหลักสูตร
                            </span>
                            <h1 class="hero-title mt-2">หลักสูตรการเรียนรู้เพื่อพัฒนาตนเอง</h1>
                            <p class="hero-subtitle">หลักสูตรทั้งหมด <?= number_format($curriculumCount) ?> ด้าน เพื่อการพัฒนาตนเองของผู้เรียนในทุกช่วงวัย</p>
                        </div>
                    </div>
                    <a href="course_show.php" class="btn-hero-view">
                        <i class="fas fa-list"></i> ดูหลักสูตรทั้งหมด
                    </a>
                </div>
            </div>

            <!-- Summary Bar -->
            <div class="summary-bar">
                <div class="summary-item">
                    <div class="summary-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <p class="summary-label">หลักสูตรทั้งหมด</p>
                        <div class="summary-value"><?= number_format($totalCourses) ?> <small>หลักสูตร</small></div>
                    </div>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-item">
                    <div class="summary-icon green">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <p class="summary-label">หมวดหลักสูตรในระบบ</p>
                        <div class="summary-value"><?= number_format($curriculumCount) ?> <small>หลักสูตร</small></div>
                    </div>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-item">
                    <div class="summary-icon orange">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div>
                        <p class="summary-label">หมวดที่มีหลักสูตรมากที่สุด</p>
                        <div class="summary-value" style="font-size: 1.1rem;"><?= $topCurriculum ? htmlspecialchars($topCurriculum['culture_name']) : '-' ?> <small>(<?= number_format($maxGroupCount) ?>)</small></div>
                    </div>
                </div>
            </div>

            <!-- Section Title -->
            <div class="section-title-wrap">
                <div>
                    <h5 class="section-title-modern">
                        <i class="fas fa-th-large text-primary"></i>
                        เลือกหลักสูตร
                    </h5>
                    <p class="section-subtitle-modern ms-3">คลิกที่การ์ดเพื่อดูรายการหลักสูตรทั้งหมดในหลักสูตรนั้นๆ</p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                    <i class="fas fa-graduation-cap me-1"></i> <?= number_format($curriculumCount) ?> หลักสูตร
                </span>
            </div>

            <!-- การ์ดหลักสูตร (สร้างจากทุกแถวในตาราง culture) -->
            <div class="row g-4">
                <?php foreach ($curriculums as $index => $curriculum):
                    $style      = $cardPalette[$index % count($cardPalette)];
                    $theme      = $style['theme'];
                    $courseCount = (int)$curriculum['course_count'];
                    $courseHours = (int)$curriculum['total_hours'];
                    $percent    = $totalCourses > 0 ? round(($courseCount / $totalCourses) * 100, 1) : 0;
                    $delayClass = 'delay-' . (($index % 3) + 1);
                ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="curriculum-card <?= $delayClass ?>">
                            <!-- ส่วนหัวสีตามหลักสูตร -->
                            <div class="curriculum-top theme-<?= $theme ?>">
                                <div class="inner">
                                    <div class="curriculum-icon">
                                        <i class="<?= $style['icon'] ?>"></i>
                                    </div>
                                    <div>
                                        <span class="curriculum-num">หลักสูตรที่ <?= $index + 1 ?></span>
                                    </div>
                                    <h2 class="curriculum-title"><?= htmlspecialchars($curriculum['culture_name']) ?></h2>
                                    <p class="curriculum-desc"><?= htmlspecialchars($curriculum['culture_objective']) ?></p>
                                </div>
                            </div>

                            <!-- ส่วนเนื้อหา -->
                            <div class="curriculum-body">
                                <div class="curriculum-stats">
                                    <div class="curriculum-stat">
                                        <div class="val"><?= number_format($courseCount) ?></div>
                                        <div class="lbl"><i class="fas fa-book me-1"></i>หลักสูตรย่อย</div>
                                    </div>
                                    <div class="curriculum-stat-divider"></div>
                                    <div class="curriculum-stat">
                                        <div class="val"><?= number_format($courseHours) ?></div>
                                        <div class="lbl"><i class="fas fa-clock me-1"></i>ชั่วโมงเรียนรวม</div>
                                    </div>
                                </div>

                                <div class="curriculum-progress">
                                    <div class="curriculum-progress-bar bar-<?= $theme ?>" style="width: <?= $percent ?>%"></div>
                                </div>
                                <div class="progress-text">
                                    <i class="fas fa-chart-pie me-1"></i><?= $percent ?>% ของหลักสูตรทั้งหมด
                                </div>

                                <a href="./course_show.php?culture_id=<?= (int)$curriculum['culture_id'] ?>" class="btn-curriculum theme-<?= $theme ?>">
                                    ดูรายการหลักสูตร <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</main>

<?php include "./modules/footer.inc.php"; ?>

<script>
    // Animation progress bar เมื่อ scroll เข้ามาเห็น
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.curriculum-progress-bar');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const width = entry.target.style.width;
                    entry.target.style.width = '0%';
                    setTimeout(() => {
                        entry.target.style.width = width;
                    }, 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        bars.forEach(bar => observer.observe(bar));
    });
</script>

</body>
</html>
