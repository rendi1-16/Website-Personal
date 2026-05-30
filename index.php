<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/functions.php';

requireLogin();
$pageTitle = 'Beranda';

$totalBuku      = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota   = $pdo->query("SELECT COUNT(*) FROM users WHERE role='anggota'")->fetchColumn();
$totalDipinjam  = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'")->fetchColumn();
$totalEbook     = $pdo->query("SELECT COUNT(*) FROM ebook")->fetchColumn();

$bukuBaru = $pdo->query(
  "SELECT b.*, k.nama_kategori FROM buku b
   LEFT JOIN kategori k ON b.id_kategori=k.id
   ORDER BY b.created_at DESC LIMIT 6"
)->fetchAll();

$kunjunganAktif = null;
if (!isAdmin()) {
  try {
    $ka = $pdo->prepare("SELECT k.*, l.nomor_loker FROM kunjungan k LEFT JOIN loker l ON k.id_loker=l.id WHERE k.id_user=? AND k.status='aktif'");
    $ka->execute([$_SESSION['user_id']]);
    $kunjunganAktif = $ka->fetch();
  } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beranda — Perpustakaan Digital Polstat STIS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<?php
// Simpan flash sebelum include header
$flash = getFlash();
?>

<!-- Top Bar -->
<div class="topbar d-none d-md-block">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex gap-4">
        <div class="topbar-item"><i class="bi bi-geo-alt"></i> Jl. Otto Iskandar Dinata No.64C, Jakarta Timur</div>
        <div class="topbar-item"><i class="bi bi-clock"></i> Senin–Jumat: 07.30–16.00 WIB</div>
      </div>
      <div class="d-flex gap-3">
        <span class="topbar-item"><i class="bi bi-person-check"></i> <?= e($_SESSION['nama']) ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">
      <div class="brand-logo">
          <img src="https://simpus.stis.ac.id/image/Logo.png" alt="Logo STIS" width="40" height="40">
      </div>
      <div>
        <div>Perpustakaan <strong>STIS</strong></div>
        <span class="brand-sub">Politeknik Statistika STIS</span>
      </div>
    </a>
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto gap-1 ms-3">
        <li class="nav-item"><a class="nav-link active" href="<?= BASE_URL ?>/index.php"><i class="bi bi-house me-1"></i>Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/anggota/katalog.php"><i class="bi bi-grid-3x3-gap me-1"></i>Katalog Buku</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/anggota/ebook.php"><i class="bi bi-file-earmark-pdf me-1"></i>E-Book</a></li>
        <?php if (!isAdmin()): ?>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/anggota/checkin.php"><i class="bi bi-door-open me-1"></i>Kunjungan</a></li>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-sliders me-1"></i>Kelola</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/dashboard.php"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/buku.php"><i class="bi bi-book me-2 text-primary"></i>Kelola Buku</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/kategori.php"><i class="bi bi-tags me-2 text-primary"></i>Kategori</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/anggota.php"><i class="bi bi-people me-2 text-primary"></i>Data Anggota</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/peminjaman.php"><i class="bi bi-arrow-left-right me-2 text-warning"></i>Peminjaman</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/kunjungan.php"><i class="bi bi-people-fill me-2 text-success"></i>Monitor Kunjungan</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/ebook.php"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Kelola E-Book</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php
          $fotoProfil = $_SESSION['foto_profil'] ?? null;
          if (!$fotoProfil) {
            try {
              $fp = $pdo->prepare("SELECT foto_profil FROM users WHERE id=?");
              $fp->execute([$_SESSION['user_id']]);
              $fotoProfil = $fp->fetchColumn();
              $_SESSION['foto_profil'] = $fotoProfil;
            } catch (Exception $e) {}
          }
        ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
            <?php if ($fotoProfil): ?>
              <img src="<?= e($fotoProfil) ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.4)">
            <?php else: ?>
              <span style="width:34px;height:34px;background:rgba(255,255,255,.18);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;border:2px solid rgba(255,255,255,.3)"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></span>
            <?php endif; ?>
            <span class="d-none d-lg-inline" style="font-size:.85rem"><?= e(explode(' ',$_SESSION['nama'])[0]) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2">
              <div class="fw-semibold" style="font-size:.875rem"><?= e($_SESSION['nama']) ?></div>
              <div class="text-muted" style="font-size:.72rem"><?= isAdmin() ? '👑 Administrator' : '🎓 Mahasiswa' ?></div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <?php if (!isAdmin()): ?>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/anggota/profil.php"><i class="bi bi-person-circle me-2"></i>Profil Saya</a></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/anggota/riwayat.php"><i class="bi bi-clock-history me-2"></i>Riwayat Pinjam</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <?php endif; ?>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<?php if ($flash): ?>
<div class="container mt-3">
  <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?> alert-dismissible fade show" role="alert">
    <?= e($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>

<!-- ══ HERO SECTION ══ -->
<section class="hero-section">
  <div class="hero-bg"></div>
  <div class="container">
    <div class="hero-content">
      <div class="hero-eyebrow">
        <i class="bi bi-stars"></i>
        Selamat Datang, <?= e(explode(' ',$_SESSION['nama'])[0]) ?>!
      </div>
      <h1 class="hero-title">
        Investasi Terbaik<br>adalah <span>Ilmu Pengetahuan.</span>
      </h1>
      <p class="hero-subtitle">
        Akses ribuan koleksi buku dan e-book Politeknik Statistika STIS. Pinjam, baca, dan kembangkan dirimu bersama kami.
      </p>
      <div class="hero-actions">
        <a href="<?= BASE_URL ?>/anggota/katalog.php" class="btn-hero-primary">
          <i class="bi bi-search"></i> Cari Buku Sekarang
        </a>
        <?php if (!isAdmin()): ?>
        <a href="<?= BASE_URL ?>/anggota/checkin.php" class="btn-hero-outline">
          <i class="bi bi-door-open"></i>
          <?= $kunjunganAktif ? 'Lihat Kunjungan Aktif' : 'Check-in ke Perpustakaan' ?>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Stat bar -->
  <div class="hero-stats">
    <div class="container">
      <div class="row g-0">
        <div class="col-6 col-md-3">
          <div class="hero-stat-item">
            <div class="hero-stat-icon" style="background:#dbeafe">📚</div>
            <div>
              <div class="hero-stat-num"><?= number_format($totalBuku) ?>+</div>
              <div class="hero-stat-lbl">Koleksi Buku</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="hero-stat-item">
            <div class="hero-stat-icon" style="background:#d1fae5">📄</div>
            <div>
              <div class="hero-stat-num"><?= $totalEbook ?>+</div>
              <div class="hero-stat-lbl">E-Book Digital</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="hero-stat-item">
            <div class="hero-stat-icon" style="background:#fef3c7">🎓</div>
            <div>
              <div class="hero-stat-num"><?= number_format($totalAnggota) ?>+</div>
              <div class="hero-stat-lbl">Anggota Terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="hero-stat-item">
            <div class="hero-stat-icon" style="background:#ffe4e6">🔒</div>
            <div>
              <div class="hero-stat-num">200</div>
              <div class="hero-stat-lbl">Loker Tersedia</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ KUNJUNGAN AKTIF BANNER ══ -->
<?php if ($kunjunganAktif): ?>
<div class="container mt-4">
  <div class="visit-banner">
    <div style="font-size:1.8rem">🔓</div>
    <div class="flex-fill">
      <div class="fw-bold" style="color:#065f46">Kamu sedang berkunjung ke perpustakaan</div>
      <div class="text-muted small">
        Loker: <strong><?= $kunjunganAktif['nomor_loker'] ?? 'Tidak pakai loker' ?></strong>
        &nbsp;·&nbsp; Masuk sejak: <strong><?= date('H:i', strtotime($kunjunganAktif['jam_masuk'])) ?> WIB</strong>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/anggota/checkin.php" class="btn btn-sm fw-semibold" style="background:#10b981;color:white;border:none">Lihat & Check-out</a>
  </div>
</div>
<?php endif; ?>

<!-- ══ LAYANAN KAMI ══ -->
<div class="container mt-5">
  <div class="text-center mb-4">
    <div class="section-label mx-auto"><i class="bi bi-grid me-1"></i>Layanan</div>
    <h2 class="section-title">Apa yang Bisa Kamu Lakukan?</h2>
    <p class="section-sub">Nikmati berbagai layanan perpustakaan digital STIS dalam satu platform</p>
  </div>
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <a href="<?= BASE_URL ?>/anggota/katalog.php" style="text-decoration:none">
        <div class="feature-box text-center">
          <div class="feature-icon mx-auto" style="background:#dbeafe"><i class="bi bi-search" style="color:#1a3a8f;font-size:1.4rem"></i></div>
          <h5>Katalog Buku</h5>
          <p>Cari dan temukan buku dari koleksi lengkap perpustakaan STIS</p>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="<?= BASE_URL ?>/anggota/ebook.php" style="text-decoration:none">
        <div class="feature-box text-center">
          <div class="feature-icon mx-auto" style="background:#fee2e2"><i class="bi bi-file-earmark-pdf" style="color:#b91c1c;font-size:1.4rem"></i></div>
          <h5>E-Book</h5>
          <p>Baca buku digital langsung di browser tanpa perlu meminjam fisik</p>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="<?= BASE_URL ?>/anggota/checkin.php" style="text-decoration:none">
        <div class="feature-box text-center">
          <div class="feature-icon mx-auto" style="background:#d1fae5"><i class="bi bi-door-open" style="color:#059669;font-size:1.4rem"></i></div>
          <h5>Kunjungan & Loker</h5>
          <p>Check-in ke perpustakaan dan gunakan loker penyimpanan barang</p>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="<?= BASE_URL ?>/anggota/riwayat.php" style="text-decoration:none">
        <div class="feature-box text-center">
          <div class="feature-icon mx-auto" style="background:#fef3c7"><i class="bi bi-clock-history" style="color:#b45309;font-size:1.4rem"></i></div>
          <h5>Riwayat Pinjam</h5>
          <p>Pantau status peminjaman dan denda keterlambatan pengembalian</p>
        </div>
      </a>
    </div>
  </div>
</div>

<!-- ══ BUKU TERBARU ══ -->
<div class="container mt-5 mb-3">
  <div class="d-flex align-items-end justify-content-between mb-4">
    <div>
      <div class="section-label"><i class="bi bi-clock me-1"></i>Terbaru</div>
      <h2 class="section-title mb-0">Koleksi Buku Terbaru</h2>
    </div>
    <a href="<?= BASE_URL ?>/anggota/katalog.php" class="btn btn-outline-primary btn-sm fw-semibold">
      Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>

  <div class="row g-3">
    <?php foreach ($bukuBaru as $b): ?>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card h-100" style="overflow:hidden">
        <?php if (!empty($b['cover_url'])): ?>
          <img src="<?= e($b['cover_url']) ?>" alt="Cover" class="book-cover">
        <?php else: ?>
          <div class="book-cover-placeholder"><i class="bi bi-book"></i></div>
        <?php endif; ?>
        <div class="card-body p-2">
          <p class="fw-semibold mb-1" style="font-size:.82rem;line-height:1.3"><?= e($b['judul']) ?></p>
          <p class="text-muted mb-1" style="font-size:.75rem"><?= e($b['pengarang']) ?></p>
          <span class="badge-<?= $b['stok_tersedia'] > 0 ? 'tersedia' : 'habis' ?>">
            <?= $b['stok_tersedia'] > 0 ? 'Tersedia' : 'Habis' ?>
          </span>
        </div>
        <div class="card-footer p-2">
          <a href="<?= BASE_URL ?>/anggota/detail_buku.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary w-100">Detail</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
