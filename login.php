<!DOCTYPE html>
<html lang="th">
<head>
    <?php include "./modules/head.inc.php"; ?>
    <title>เข้าสู่ระบบ | C-Smart</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Sarabun', 'Prompt', sans-serif;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin: 0;
            /* อนุญาตให้เลื่อนลงได้ กรณีจอเตี้ยมาก เช่น มือถือแนวนอน */
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* พื้นหลัง gradient เคลื่อนไหว (แยกเป็นเลเยอร์ fixed ไม่ทำให้เกิด scrollbar) */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: -2;
            background: linear-gradient(-45deg, #1e3c72, #2a5298, #0d6efd, #6dd5ed);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ฟองวงกลมลอยพื้นหลัง */
        .bg-bubbles {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-bubbles::before,
        .bg-bubbles::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .bg-bubbles::before {
            width: 260px;
            height: 260px;
            top: -90px;
            left: -90px;
        }

        .bg-bubbles::after {
            width: 340px;
            height: 340px;
            bottom: -130px;
            right: -130px;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, 50px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.95); }
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 18px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 50%, #084298 100%);
            color: white;
            padding: 1.25rem 1.25rem 1.1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header .icon-wrapper {
            width: 46px;
            height: 46px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .login-header .icon-wrapper i {
            font-size: 1.25rem;
            color: white;
        }

        .login-header .logo {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .login-header .subtitle {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-top: 2px;
            font-weight: 300;
        }

        .login-body {
            padding: 1.4rem 1.5rem 1.25rem;
        }

        .login-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            text-align: center;
            margin-bottom: 2px;
        }

        .login-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 1.1rem;
        }

        .form-label {
            font-weight: 500;
            color: #475569;
            font-size: 0.8rem;
            margin-bottom: 0.3rem;
        }

        .form-control {
            padding: 10px 14px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            font-size: 0.9rem;
            background: #f8fafc;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            outline: none;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: #64748b;
            border-radius: 10px 0 0 10px;
            padding: 0 14px;
            transition: all 0.25s ease;
        }

        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
            background: #ffffff;
            color: #0d6efd;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        /* ปุ่มแสดง/ซ่อนรหัสผ่าน */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            padding: 6px;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #0d6efd;
        }

        .btn-login {
            padding: 11px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
            transition: all 0.25s ease;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-left: 4px solid #dc3545;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 10px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .login-footer {
            text-align: center;
            padding: 0.65rem 1rem;
            background: #f8fafc;
            font-size: 0.75rem;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            line-height: 1.5;
        }

        .login-footer strong {
            color: #1e293b;
            font-weight: 600;
        }

        .login-footer small {
            color: #64748b;
        }

        /* จอสูงน้อย (laptop 768px ลงมา) — ลดระยะห่างเพิ่มอีก */
        @media (max-height: 700px) {
            body { padding: 10px; }
            .login-header { padding: 0.9rem 1.25rem 0.8rem; }
            .login-header .icon-wrapper { width: 38px; height: 38px; margin-bottom: 0.35rem; }
            .login-header .icon-wrapper i { font-size: 1rem; }
            .login-header .logo { font-size: 1.2rem; }
            .login-body { padding: 1.1rem 1.4rem 1rem; }
            .login-title { font-size: 1rem; }
            .login-subtitle { margin-bottom: 0.8rem; }
            .form-control { padding: 9px 12px; }
            .btn-login { padding: 10px; }
            .login-card { max-width: 380px; }
        }

        @media (max-width: 480px) {
            .login-card {
                max-width: 100%;
                border-radius: 16px;
            }
            .login-body {
                padding: 1.3rem 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <div class="bg-bubbles"></div>

    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="icon-wrapper">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo">C-Smart</div>
            <div class="subtitle">ระบบบริหารจัดการการศึกษาต่อเนื่อง</div>
        </div>

        <!-- Body -->
        <div class="login-body">
            <div class="login-title">ยินดีต้อนรับกลับ 👋</div>
            <div class="login-subtitle">กรุณาเข้าสู่ระบบเพื่อใช้งาน</div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert py-2 px-3 mb-3 small">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <?php
                        if ($_GET['error'] == '1') echo 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
                        elseif ($_GET['error'] == '2') echo 'กรุณากรอกข้อมูลให้ครบถ้วน';
                        else echo 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง';
                    ?>
                </div>
            <?php endif; ?>

            <form action="./login_check.php" method="post">
                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-school me-1 text-primary"></i> รหัสสถานศึกษา
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text"
                               class="form-control"
                               name="username_schoolid"
                               placeholder="กรอกรหัสสถานศึกษา"
                               required
                               autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-key me-1 text-primary"></i> รหัสผ่าน
                    </label>
                    <div class="input-group" style="position: relative;">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password"
                               class="form-control"
                               name="password_schoolid"
                               id="passwordInput"
                               placeholder="กรอกรหัสผ่าน"
                               required
                               style="padding-right: 42px;">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ
                    </button>
                </div>
            </form>

            <div class="text-center mt-3" style="font-size: 0.72rem; color: #94a3b8;">
                <i class="fas fa-shield-alt text-success me-1"></i>
                การเชื่อมต่อของคุณได้รับการเข้ารหัส
            </div>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <strong>ศูนย์ส่งเสริมการเรียนรู้ระดับอำเภอบ้านบึง</strong>
            <small>สกร. จังหวัดชลบุรี © <?php echo date('Y') + 543; ?></small>
        </div>
    </div>

    <script>
        // ฟังก์ชันแสดง/ซ่อนรหัสผ่าน
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
