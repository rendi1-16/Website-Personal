-- ============================================
-- TAMBAHAN DATABASE: fitur kunjungan & loker
-- Jalankan di phpMyAdmin → perpustakaan_digital
-- ============================================

USE perpustakaan_digital;

-- Tabel loker (nomor loker yang tersedia)
CREATE TABLE IF NOT EXISTS loker (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_loker VARCHAR(10) NOT NULL UNIQUE,
  status ENUM('kosong','terpakai') DEFAULT 'kosong',
  id_kunjungan INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel kunjungan mahasiswa
CREATE TABLE IF NOT EXISTS kunjungan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  tujuan ENUM('belajar','meminjam_buku','mengembalikan_buku') NOT NULL,
  pakai_loker TINYINT(1) DEFAULT 0,
  id_loker INT DEFAULT NULL,
  jam_masuk DATETIME NOT NULL,
  jam_keluar DATETIME DEFAULT NULL,
  status ENUM('aktif','selesai') DEFAULT 'aktif',
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_loker) REFERENCES loker(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambah kolom cover_url ke buku jika belum ada
ALTER TABLE buku MODIFY COLUMN cover_url VARCHAR(255) DEFAULT NULL;

-- Isi data loker A01 - A20
INSERT IGNORE INTO loker (nomor_loker) VALUES
('A01'),('A02'),('A03'),('A04'),('A05'),
('A06'),('A07'),('A08'),('A09'),('A10'),
('B01'),('B02'),('B03'),('B04'),('B05'),
('B06'),('B07'),('B08'),('B09'),('B10');
