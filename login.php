<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/functions.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim  = trim($_POST['nim_nip'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($nim && $pass) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nim_nip = ?");
        $stmt->execute([$nim]);
        $user = $stmt->fetch();
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = $user['role'];
            $redirect = $user['role'] === 'admin' ? '/admin/dashboard.php' : '/anggota/katalog.php';
            header('Location: ' . BASE_URL . $redirect);
            exit;
        } else { $error = 'NIM atau password salah.'; }
    } else { $error = 'Semua kolom wajib diisi.'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Perpustakaan Digital STIS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:wght@500;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      background: #0f172a;
    }

    /* Panel kiri — visual */
    .login-visual {
      flex: 1;
      background: linear-gradient(145deg, #1e3a8a 0%, #2563eb 40%, #7c3aed 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }
    .login-visual::before {
      content:'';
      position:absolute; top:-80px; right:-80px;
      width:320px; height:320px;
      background: rgba(255,255,255,.06); border-radius:50%;
    }
    .login-visual::after {
      content:'';
      position:absolute; bottom:-100px; left:-60px;
      width:400px; height:400px;
      background: rgba(255,255,255,.04); border-radius:50%;
    }
    .visual-orb {
      width: 160px; height: 160px;
      background: rgba(255,255,255,.12);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 4.5rem;
      margin-bottom: 2rem;
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.2);
      animation: float 4s ease-in-out infinite;
    }
    @keyframes float {
      0%,100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }
    .visual-title {
      font-family: 'Lora', serif;
      color: white;
      font-size: 1.75rem;
      font-weight: 600;
      text-align: center;
      line-height: 1.3;
      margin-bottom: .75rem;
      position: relative; z-index:1;
    }
    .visual-sub {
      color: rgba(255,255,255,.7);
      text-align: center;
      font-size: .9rem;
      font-weight: 500;
      position: relative; z-index:1;
    }
    .visual-stats {
      display: flex;
      gap: 1.5rem;
      margin-top: 2.5rem;
      position: relative; z-index:1;
    }
    .vstat {
      text-align: center;
      background: rgba(255,255,255,.1);
      border: 1px solid rgba(255,255,255,.15);
      border-radius: 12px;
      padding: .75rem 1.25rem;
      backdrop-filter: blur(6px);
    }
    .vstat-num { font-size: 1.4rem; font-weight: 800; color: white; }
    .vstat-lbl { font-size: .7rem; color: rgba(255,255,255,.65); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }

    /* Panel kanan — form */
    .login-form-wrap {
      width: 420px;
      background: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 2.5rem;
    }
    .logo-wrap {
      text-align: center;
      margin-bottom: 1.75rem;
    }
    .logo-wrap img {
      height: 72px;
      object-fit: contain;
      margin-bottom: .75rem;
    }
    .logo-wrap .inst-name {
      font-size: .72rem;
      font-weight: 700;
      color: #1e3a8a;
      text-transform: uppercase;
      letter-spacing: .08em;
      line-height: 1.4;
    }
    .form-title {
      font-family: 'Lora', serif;
      font-size: 1.6rem;
      font-weight: 600;
      color: #0f172a;
      margin-bottom: .25rem;
    }
    .form-sub { font-size: .875rem; color: #64748b; margin-bottom: 1.75rem; }

    .input-group-modern { position: relative; margin-bottom: 1rem; }
    .input-group-modern .input-icon {
      position: absolute;
      left: .9rem; top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 1rem;
      z-index: 2;
    }
    .input-group-modern input {
      width: 100%;
      padding: .7rem .9rem .7rem 2.4rem;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: .9rem;
      font-family: 'Plus Jakarta Sans', sans-serif;
      transition: border-color .15s, box-shadow .15s;
    }
    .input-group-modern input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .input-group-modern label {
      display: block;
      font-size: .8rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: .35rem;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .btn-login {
      width: 100%;
      padding: .75rem;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: .95rem;
      font-weight: 700;
      font-family: 'Plus Jakarta Sans', sans-serif;
      cursor: pointer;
      transition: all .2s;
      box-shadow: 0 4px 14px rgba(37,99,235,.35);
      margin-top: .5rem;
    }
    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(37,99,235,.45);
    }
    .error-box {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
      border-radius: 10px;
      padding: .7rem 1rem;
      font-size: .875rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }
    .divider { display:flex; align-items:center; gap:.75rem; margin:1.25rem 0; }
    .divider::before,.divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }
    .divider span { font-size:.75rem; color:#94a3b8; font-weight:500; white-space:nowrap; }
    .register-link {
      text-align: center;
      font-size: .875rem;
      color: #64748b;
    }
    .register-link a { color: #2563eb; font-weight: 700; text-decoration: none; }
    .register-link a:hover { text-decoration: underline; }
    .demo-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: .75rem 1rem;
      font-size: .78rem;
      color: #475569;
      margin-top: 1.25rem;
      width: 100%;
    }
    .demo-box strong { color: #1e293b; }
    .demo-box code { background: #e0e7ff; color: #3730a3; padding: .1em .4em; border-radius: 4px; font-size: .78rem; }

    /* Responsive */
    @media (max-width: 768px) {
      .login-visual { display: none; }
      .login-form-wrap { width: 100%; padding: 2rem 1.5rem; min-height: 100vh; }
    }
  </style>
</head>
<body>

<!-- Panel kiri -->
<div class="login-visual">
  <div class="visual-orb">
      <img src="https://simpus.stis.ac.id/image/Logo.png" alt="Logo STIS" width="160" height="160">
  </div>
  <div class="visual-title">Perpustakaan Digital<br>Politeknik Statistika STIS</div>
  <div class="visual-sub">Temukan, Pinjam, dan Kelola<br>Koleksi Buku Kampus dengan Mudah</div>
  <div class="visual-stats">
    <div class="vstat">
      <div class="vstat-num">500+</div>
      <div class="vstat-lbl">Koleksi Buku</div>
    </div>
    <div class="vstat">
      <div class="vstat-num">200</div>
      <div class="vstat-lbl">Loker Tersedia</div>
    </div>
    <div class="vstat">
      <div class="vstat-num">24/7</div>
      <div class="vstat-lbl">Akses Digital</div>
    </div>
  </div>
</div>

<!-- Panel kanan — form -->
<div class="login-form-wrap">
  <div class="logo-wrap">
    <img src="https://stis.ac.id/media/source/up.png"
         alt="Logo Polstat STIS"
         onerror="this.style.display='none'">
    <div class="inst-name">Politeknik Statistika STIS<br>Indonesia</div>
  </div>

  <div class="form-title">Selamat Datang</div>
  <div class="form-sub">Masuk untuk mengakses perpustakaan digital</div>

  <?php if ($error): ?>
  <div class="error-box" style="width:100%">
    <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" style="width:100%">
    <div class="input-group-modern">
      <label>NIM</label>
      <span class="input-icon"><i class="bi bi-person-badge"></i></span>
      <input type="text" name="nim_nip" placeholder="Masukkan NIM kamu"
             value="<?= e($_POST['nim_nip'] ?? '') ?>" required autocomplete="username">
    </div>
    <div class="input-group-modern">
      <label>Password</label>
      <span class="input-icon"><i class="bi bi-lock"></i></span>
      <input type="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
    </div>
    <button type="submit" class="btn-login">
      <i class="bi bi-box-arrow-in-right me-2"></i>Login
    </button>
  </form>

  <div class="divider"><span>atau</span></div>
  <div class="register-link">
    Belum punya akun? <a href="<?= BASE_URL ?>/register.php">Daftar di sini</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"></script>
</body>
</html>
