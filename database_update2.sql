-- ============================================
-- UPDATE DATABASE: fitur baru
-- Jalankan di phpMyAdmin → perpustakaan_digital
-- ============================================

USE perpustakaan_digital;

-- 1. Tambah kolom foto_profil ke tabel users
ALTER TABLE users ADD COLUMN IF NOT EXISTS foto_profil VARCHAR(255) DEFAULT NULL;

-- 2. Tambah loker sampai 200 (A01-A20 sudah ada, tambah sisanya)
-- Baris A sudah ada, tambah B-J (masing-masing 20)
INSERT IGNORE INTO loker (nomor_loker) VALUES
('B01'),('B02'),('B03'),('B04'),('B05'),('B06'),('B07'),('B08'),('B09'),('B10'),
('B11'),('B12'),('B13'),('B14'),('B15'),('B16'),('B17'),('B18'),('B19'),('B20'),
('C01'),('C02'),('C03'),('C04'),('C05'),('C06'),('C07'),('C08'),('C09'),('C10'),
('C11'),('C12'),('C13'),('C14'),('C15'),('C16'),('C17'),('C18'),('C19'),('C20'),
('D01'),('D02'),('D03'),('D04'),('D05'),('D06'),('D07'),('D08'),('D09'),('D10'),
('D11'),('D12'),('D13'),('D14'),('D15'),('D16'),('D17'),('D18'),('D19'),('D20'),
('E01'),('E02'),('E03'),('E04'),('E05'),('E06'),('E07'),('E08'),('E09'),('E10'),
('E11'),('E12'),('E13'),('E14'),('E15'),('E16'),('E17'),('E18'),('E19'),('E20'),
('F01'),('F02'),('F03'),('F04'),('F05'),('F06'),('F07'),('F08'),('F09'),('F10'),
('F11'),('F12'),('F13'),('F14'),('F15'),('F16'),('F17'),('F18'),('F19'),('F20'),
('G01'),('G02'),('G03'),('G04'),('G05'),('G06'),('G07'),('G08'),('G09'),('G10'),
('G11'),('G12'),('G13'),('G14'),('G15'),('G16'),('G17'),('G18'),('G19'),('G20'),
('H01'),('H02'),('H03'),('H04'),('H05'),('H06'),('H07'),('H08'),('H09'),('H10'),
('H11'),('H12'),('H13'),('H14'),('H15'),('H16'),('H17'),('H18'),('H19'),('H20'),
('I01'),('I02'),('I03'),('I04'),('I05'),('I06'),('I07'),('I08'),('I09'),('I10'),
('I11'),('I12'),('I13'),('I14'),('I15'),('I16'),('I17'),('I18'),('I19'),('I20'),
('J01'),('J02'),('J03'),('J04'),('J05'),('J06'),('J07'),('J08'),('J09'),('J10'),
('J11'),('J12'),('J13'),('J14'),('J15'),('J16'),('J17'),('J18'),('J19'),('J20');

-- 3. Tabel e-book
CREATE TABLE IF NOT EXISTS ebook (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  pengarang VARCHAR(100) NOT NULL,
  penerbit VARCHAR(100),
  tahun_terbit YEAR,
  id_kategori INT,
  deskripsi TEXT,
  cover_url VARCHAR(255),
  file_url VARCHAR(255) NOT NULL,
  ukuran_file VARCHAR(20),
  jumlah_halaman INT,
  total_diakses INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Contoh data e-book (pakai link PDF publik)
INSERT INTO ebook (judul, pengarang, penerbit, tahun_terbit, id_kategori, deskripsi, file_url, ukuran_file, jumlah_halaman) VALUES
('Pengantar Statistika Deskriptif', 'Tim Dosen STIS', 'Polstat STIS', 2023, 1, 'Modul pengantar statistika deskriptif untuk mahasiswa semester 1.', 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/pdf-sample.pdf', '2.1 MB', 45),
('Dasar-Dasar Pemrograman Web', 'Tim Lab Komputer STIS', 'Polstat STIS', 2023, 2, 'Panduan lengkap HTML, CSS, JavaScript, dan PHP untuk pemula.', 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/pdf-sample.pdf', '3.5 MB', 120),
('Matematika Statistika', 'Tim Dosen STIS', 'Polstat STIS', 2022, 3, 'Kalkulus dan aljabar linear untuk statistika terapan.', 'https://www.w3.org/WAI/WCAG21/Techniques/pdf/pdf-sample.pdf', '4.2 MB', 200);
