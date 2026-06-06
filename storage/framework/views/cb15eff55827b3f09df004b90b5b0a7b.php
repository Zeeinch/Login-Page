<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Laravel Auth</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET & BASE ===== */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: #e2e8f0;
            overflow: hidden;
            position: relative;
        }

        /* ===== ANIMATED BACKGROUND BLOBS ===== */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: .35;
            z-index: 0;
            animation: float 8s ease-in-out infinite alternate;
        }
        body::before {
            width: 500px; height: 500px;
            background: #06b6d4;
            top: -120px; right: -80px;
        }
        body::after {
            width: 400px; height: 400px;
            background: #7c3aed;
            bottom: -100px; left: -60px;
            animation-delay: 2s;
        }
        @keyframes float {
            to { transform: translate(40px, 30px) scale(1.08); }
        }

        /* ===== GLASS CARD ===== */
        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 20px;
            padding: 44px 36px 36px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .35);
            animation: slideUp .6s cubic-bezier(.22,1,.36,1);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== LOGO / ICON ===== */
        .auth-icon {
            width: 56px; height: 56px;
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #06b6d4, #7c3aed);
            box-shadow: 0 4px 16px rgba(6, 182, 212, .35);
        }
        .auth-icon svg { width: 28px; height: 28px; fill: #fff; }

        /* ===== HEADINGS ===== */
        .auth-card h1 {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -.02em;
        }
        .auth-card .subtitle {
            text-align: center;
            font-size: .875rem;
            color: #94a3b8;
            margin-bottom: 28px;
        }

        /* ===== ALERTS ===== */
        .alert-error {
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: .85rem;
            margin-bottom: 20px;
            animation: shake .45s ease;
        }
        .alert-error ul { list-style: none; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        /* ===== FORM ELEMENTS ===== */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 10px;
            background: rgba(255, 255, 255, .06);
            color: #f1f5f9;
            font-size: .95rem;
            font-family: inherit;
            outline: none;
            transition: border-color .25s, box-shadow .25s;
        }
        .form-group input::placeholder { color: #64748b; }
        .form-group input:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, .25);
        }

        /* ===== SUBMIT BUTTON ===== */
        .btn-submit {
            display: block;
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #06b6d4, #7c3aed);
            box-shadow: 0 4px 14px rgba(6, 182, 212, .35);
            transition: transform .2s, box-shadow .2s;
            margin-top: 6px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 182, 212, .45);
        }
        .btn-submit:active { transform: translateY(0); }

        /* ===== LINK ===== */
        .auth-footer {
            text-align: center;
            margin-top: 22px;
            font-size: .85rem;
            color: #94a3b8;
        }
        .auth-footer a {
            color: #67e8f9;
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }
        .auth-footer a:hover { color: #a5f3fc; }
    </style>
</head>
<body>

<div class="auth-card" id="registerCard">
    
    <div class="auth-icon">
        <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
    </div>

    <h1>Buat Akun Baru</h1>
    <p class="subtitle">Daftarkan diri Anda untuk mulai menggunakan aplikasi</p>

    
    <?php if($errors->any()): ?>
        <div class="alert-error" id="alertError">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form method="POST" action="/register" id="registerForm">
        <?php echo csrf_field(); ?> 

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" placeholder="John Doe" value="<?php echo e(old('name')); ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="nama@email.com" value="<?php echo e(old('email')); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Min. 6 karakter" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-submit" id="btnRegister">Daftar</button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="<?php echo e(route('login')); ?>">Masuk di sini</a>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\laragon\www\latihan-database2\resources\views/auth/register.blade.php ENDPATH**/ ?>