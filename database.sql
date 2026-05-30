-- ============================================
-- DATABASE: perpustakaan_digital
-- Politeknik Statistika STIS
-- ============================================

CREATE DATABASE IF NOT EXISTS perpustakaan_digital
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE perpustakaan_digital;

-- ============================================
-- TABEL: users
-- ============================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  nim_nip VARCHAR(20) UNIQUE NOT NULL,
  role ENUM('admin','anggota') DEFAULT 'anggota',
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: kategori
-- ============================================
CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(50) NOT NULL,
  deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: buku
-- ============================================
CREATE TABLE buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  pengarang VARCHAR(100) NOT NULL,
  penerbit VARCHAR(100),
  tahun_terbit YEAR,
  isbn VARCHAR(20) UNIQUE,
  id_kategori INT,
  stok_total INT DEFAULT 1,
  stok_tersedia INT DEFAULT 1,
  sinopsis TEXT,
  cover_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: peminjaman
-- ============================================
CREATE TABLE peminjaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_buku INT NOT NULL,
  tgl_pinjam DATE NOT NULL,
  tgl_kembali_rencana DATE NOT NULL,
  tgl_kembali_aktual DATE DEFAULT NULL,
  status ENUM('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  denda INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_buku) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATA AWAL: users
-- password: admin123 (hashed)
-- ============================================
INSERT INTO users (nama, nim_nip, role, email, password) VALUES
('Administrator', 'ADMIN001', 'admin', 'admin@perpus.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Budi Santoso', '221810001', 'anggota', 'budi@mahasiswa.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Siti Rahayu', '221810002', 'anggota', 'siti@mahasiswa.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ahmad Fauzi', '221810003', 'anggota', 'ahmad@mahasiswa.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- DATA AWAL: kategori
-- ============================================
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Statistika', 'Buku-buku tentang ilmu statistika dan analisis data'),
('Pemrograman', 'Buku tentang pemrograman dan pengembangan perangkat lunak'),
('Matematika', 'Buku matematika dasar hingga lanjutan'),
('Ekonomi', 'Buku ilmu ekonomi, akuntansi, dan keuangan'),
('Fiksi', 'Novel dan karya fiksi sastra');

-- ============================================
-- DATA AWAL: buku (minimal 10 baris)
-- ============================================
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, id_kategori, stok_total, stok_tersedia, sinopsis) VALUES
('Pengantar Statistika', 'Ronald E. Walpole', 'Erlangga', 2012, '978-979-099-123-1', 1, 3, 3, 'Buku pengantar statistika yang komprehensif mencakup probabilitas, distribusi, dan inferensi statistik.'),
('Statistika untuk Penelitian', 'Sugiyono', 'Alfabeta', 2019, '978-602-289-456-2', 1, 2, 2, 'Panduan lengkap statistika untuk penelitian ilmiah dengan contoh aplikasi nyata.'),
('Analisis Regresi', 'Gujarati', 'Salemba Empat', 2015, '978-979-061-789-3', 1, 2, 1, 'Pembahasan mendalam tentang analisis regresi linier dan non-linier.'),
('PHP & MySQL untuk Pemula', 'Jubilee Enterprise', 'Elex Media', 2020, '978-623-00-1234-4', 2, 4, 4, 'Panduan belajar PHP dan MySQL dari nol hingga mahir dengan proyek nyata.'),
('Belajar Python', 'Eric Matthes', 'No Starch Press', 2021, '978-1-59327-892-5', 2, 3, 3, 'Pengantar pemrograman Python yang ramah pemula dengan proyek seru.'),
('Kalkulus Jilid 1', 'James Stewart', 'Cengage', 2016, '978-1-285-74062-6', 3, 5, 5, 'Buku kalkulus standar internasional yang digunakan di berbagai universitas ternama.'),
('Aljabar Linear', 'Gilbert Strang', 'MIT Press', 2016, '978-0-9802327-7-6', 3, 3, 2, 'Pengantar aljabar linear dengan aplikasi dalam komputasi dan machine learning.'),
('Ekonomi Makro', 'N. Gregory Mankiw', 'Erlangga', 2018, '978-979-099-567-8', 4, 4, 4, 'Prinsip-prinsip ekonomi makro yang ditulis dengan bahasa yang mudah dipahami.'),
('Akuntansi Dasar', 'Hery', 'Grasindo', 2017, '978-602-05-9876-9', 4, 2, 2, 'Dasar-dasar akuntansi keuangan untuk mahasiswa dan praktisi bisnis.'),
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '978-979-1227-00-0', 5, 3, 3, 'Novel inspiratif tentang perjuangan anak-anak Belitong menggapai mimpi melalui pendidikan.'),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, '978-979-483-000-1', 5, 2, 2, 'Karya sastra monumental yang mengisahkan perjuangan di masa kolonial Belanda.'),
('Machine Learning dengan Python', 'Aurélien Géron', 'O\'Reilly', 2022, '978-1-492-03264-2', 2, 2, 2, 'Panduan praktis machine learning menggunakan Scikit-Learn, Keras, dan TensorFlow.');

-- ============================================
-- DATA AWAL: peminjaman
-- ============================================
INSERT INTO peminjaman (id_user, id_buku, tgl_pinjam, tgl_kembali_rencana, tgl_kembali_aktual, status, denda) VALUES
(2, 3, '2025-05-01', '2025-05-15', '2025-05-14', 'dikembalikan', 0),
(3, 1, '2025-05-10', '2025-05-24', NULL, 'dipinjam', 0),
(4, 7, '2025-05-05', '2025-05-19', '2025-05-25', 'dikembalikan', 6000),
(2, 4, '2025-05-18', '2025-06-01', NULL, 'dipinjam', 0);
