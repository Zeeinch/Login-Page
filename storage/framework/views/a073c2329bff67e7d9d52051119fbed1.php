<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Laravel Auth</title>
    
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
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: #e2e8f0;
            position: relative;
        }

        /* ===== ANIMATED BACKGROUND BLOBS ===== */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(140px);
            opacity: .2;
            z-index: 0;
            animation: float 10s ease-in-out infinite alternate;
        }
        body::before {
            width: 600px; height: 600px;
            background: #7c3aed;
            top: -200px; left: 50%;
        }
        body::after {
            width: 500px; height: 500px;
            background: #06b6d4;
            bottom: -150px; right: -100px;
            animation-delay: 3s;
        }
        @keyframes float {
            to { transform: translate(40px, 30px) scale(1.08); }
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            background: rgba(255, 255, 255, .04);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .navbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -.02em;
        }
        .navbar .brand .brand-icon {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #7c3aed, #06b6d4);
        }
        .navbar .brand .brand-icon svg { width: 20px; height: 20px; fill: #fff; }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .navbar .user-name {
            font-size: .9rem;
            color: #cbd5e1;
        }
        .navbar .user-name strong { color: #f1f5f9; }
        .btn-logout {
            padding: 8px 18px;
            border: 1px solid rgba(239, 68, 68, .4);
            border-radius: 8px;
            background: rgba(239, 68, 68, .1);
            color: #fca5a5;
            font-family: inherit;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .btn-logout:hover {
            background: rgba(239, 68, 68, .2);
            transform: translateY(-1px);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            position: relative;
            z-index: 1;
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* ===== WELCOME BANNER ===== */
        .welcome-banner {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 36px 40px;
            margin-bottom: 28px;
            animation: slideUp .5s cubic-bezier(.22,1,.36,1);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .welcome-banner h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -.02em;
            margin-bottom: 6px;
        }
        .welcome-banner h1 span {
            background: linear-gradient(135deg, #a78bfa, #67e8f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .welcome-banner p {
            color: #94a3b8;
            font-size: .95rem;
            line-height: 1.6;
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 24px;
            animation: slideUp .6s cubic-bezier(.22,1,.36,1);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:nth-child(2) { animation-delay: .1s; }
        .stat-card:nth-child(3) { animation-delay: .2s; }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            margin-bottom: 14px;
        }
        .stat-card .stat-icon svg { width: 22px; height: 22px; fill: #fff; }
        .stat-card:nth-child(1) .stat-icon { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        .stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, #06b6d4, #67e8f9); }
        .stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-card .stat-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* ===== INFO SECTION ===== */
        .info-card {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 28px;
            animation: slideUp .7s cubic-bezier(.22,1,.36,1);
        }
        .info-card h2 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-card h2 svg { width: 20px; height: 20px; fill: #a78bfa; }
        .info-list { list-style: none; }
        .info-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            font-size: .9rem;
            color: #cbd5e1;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-list li .label {
            color: #94a3b8;
            min-width: 120px;
            font-weight: 500;
        }
        .info-list li .value { color: #f1f5f9; font-weight: 600; }
    </style>
</head>
<body>

    
    <nav class="navbar" id="navbar">
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            Laravel Auth
        </div>
        <div class="user-info">
            <span class="user-name">Halo, <strong><?php echo e(Auth::user()->name); ?></strong></span>
            
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-logout" id="btnLogout">Logout</button>
            </form>
        </div>
    </nav>

    
    <div class="main-content">

        
        <div class="welcome-banner" id="welcomeBanner">
            <h1>Selamat Datang, <span><?php echo e(Auth::user()->name); ?>!</span></h1>
            <p>Anda berhasil masuk ke dashboard. Sistem autentikasi Laravel Anda berjalan dengan baik. Halaman ini dilindungi dan hanya bisa diakses oleh pengguna yang sudah login.</p>
        </div>

        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="stat-label">Status</div>
                <div class="stat-value" style="color:#86efac;">Aktif ✓</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                </div>
                <div class="stat-label">Keamanan</div>
                <div class="stat-value" style="color:#67e8f9;">Terproteksi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                </div>
                <div class="stat-label">Login Terakhir</div>
                <div class="stat-value"><?php echo e(now()->format('H:i')); ?></div>
            </div>
        </div>

        
        <div class="info-card" id="infoCard">
            <h2>
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Informasi Akun
            </h2>
            <ul class="info-list">
                <li>
                    <span class="label">Nama</span>
                    <span class="value"><?php echo e(Auth::user()->name); ?></span>
                </li>
                <li>
                    <span class="label">Email</span>
                    <span class="value"><?php echo e(Auth::user()->email); ?></span>
                </li>
                <li>
                    <span class="label">Bergabung</span>
                    <span class="value"><?php echo e(Auth::user()->created_at->format('d M Y, H:i')); ?></span>
                </li>
                <li>
                    <span class="label">ID User</span>
                    <span class="value">#<?php echo e(Auth::user()->id); ?></span>
                </li>
            </ul>
        </div>

    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\latihan-database2\resources\views/auth/dashboard.blade.php ENDPATH**/ ?>