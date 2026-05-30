<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/functions.php';

if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $nim   = trim($_POST['nim_nip'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (!$nama)  $errors[] = 'Nama wajib diisi.';
    if (!$nim)   $errors[] = 'NIM wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($pass) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($pass !== $pass2)  $errors[] = 'Konfirmasi password tidak cocok.';

    if (!$errors) {
        $cek = $pdo->prepare("SELECT id FROM users WHERE nim_nip=? OR email=?");
        $cek->execute([$nim, $email]);
        if ($cek->fetch()) {
            $errors[] = 'NIM atau email sudah terdaftar.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama,nim_nip,email,password) VALUES (?,?,?,?)");
            $stmt->execute([$nama, $nim, $email, $hash]);
            setFlash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: ' . BASE_URL . '/login.php'); exit;
        }
    }
}

$pageTitle = 'Daftar Akun';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-3">
  <div class="col-md-6 col-lg-5">

    <div class="text-center mb-4">
      <div class="section-chip"><i class="bi bi-person-plus me-1"></i>Pendaftaran Akun Mahasiswa Baru</div>
      <h1 class="page-title">Buat Akun Mahasiswa</h1>
      <p class="text-muted">Daftarkan diri kamu untuk akses perpustakaan digital STIS</p>
    </div>

    <?php if ($errors): ?>
    <div class="alert alert-danger">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <ul class="mb-0 ps-3 mt-1">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body p-4">
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Nama sesuai Kartu Tanda Mahasiswa Politeknik Statistika STIS"
                   value="<?= e($_POST['nama'] ?? '') ?>" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">NIM</label>
              <input type="text" name="nim_nip" class="form-control" placeholder="Masukkan 9 digit"
                     value="<?= e($_POST['nim_nip'] ?? '') ?>" required>
            </div>
            <div class="col-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="email@stis.ac.id"
                     value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-6">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" minlength="6" required>
            </div>
            <div class="col-6">
              <label class="form-label">Konfirmasi</label>
              <input type="password" name="password2" class="form-control" placeholder="Ulangi password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-person-check me-2"></i>Daftar Sekarang
          </button>
        </form>
      </div>
    </div>

    <p class="text-center mt-3 text-muted small">
      Sudah punya akun? <a href="<?= BASE_URL ?>/login.php" class="fw-semibold">Login di sini</a>
    </p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
