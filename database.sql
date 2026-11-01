-- ============================================================
-- PinjamBuku - skema database & data awal
-- MySQL/MariaDB, utf8mb4
-- Import sekali: mysql -u root -e "CREATE DATABASE IF NOT EXISTS pinjambuku" lalu import file ini.
-- File ini memakai CREATE TABLE IF NOT EXISTS + INSERT IGNORE sehingga aman diimpor ulang.
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) DEFAULT NULL,
  google_id VARCHAR(64) DEFAULT NULL,
  avatar_url VARCHAR(500) DEFAULT NULL,
  last_login_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_google_id (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  description TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS books (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  author VARCHAR(150) NOT NULL,
  publisher VARCHAR(150) DEFAULT NULL,
  publish_year SMALLINT UNSIGNED DEFAULT NULL,
  isbn VARCHAR(30) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  cover_url VARCHAR(500) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_books_slug (slug),
  KEY idx_books_category (category_id),
  KEY idx_books_active (is_active),
  CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS book_variants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  book_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  stock_total INT UNSIGNED NOT NULL DEFAULT 0,
  stock_available INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_variants_book_name (book_id, name),
  KEY idx_variants_book (book_id),
  CONSTRAINT fk_variants_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NOT NULL,
  duration_days TINYINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_user_variant (user_id, variant_id),
  KEY idx_cart_user (user_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_variant FOREIGN KEY (variant_id) REFERENCES book_variants (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loans (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(30) DEFAULT NULL,
  user_id INT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  pickup_deadline DATETIME DEFAULT NULL,
  picked_up_at DATETIME DEFAULT NULL,
  returned_at DATETIME DEFAULT NULL,
  rejection_reason VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_loans_code (code),
  KEY idx_loans_user (user_id),
  KEY idx_loans_status (status),
  KEY idx_loans_deadline (pickup_deadline),
  CONSTRAINT fk_loans_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loan_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  loan_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NOT NULL,
  duration_days TINYINT UNSIGNED NOT NULL,
  due_date DATE DEFAULT NULL,
  returned_at DATETIME DEFAULT NULL,
  fine_amount INT UNSIGNED NOT NULL DEFAULT 0,
  fine_paid TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_loan_items_loan (loan_id),
  KEY idx_loan_items_book (book_id),
  KEY idx_loan_items_variant (variant_id),
  CONSTRAINT fk_loan_items_loan FOREIGN KEY (loan_id) REFERENCES loans (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_loan_items_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_loan_items_variant FOREIGN KEY (variant_id) REFERENCES book_variants (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(80) NOT NULL,
  setting_value TEXT DEFAULT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_views (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  session_id VARCHAR(64) NOT NULL,
  path VARCHAR(255) NOT NULL,
  book_id INT UNSIGNED DEFAULT NULL,
  ip_hash CHAR(64) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_views_created (created_at),
  KEY idx_views_book (book_id, created_at),
  CONSTRAINT fk_views_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limits (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  identifier VARCHAR(191) NOT NULL,
  attempts SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  window_started_at DATETIME NOT NULL,
  blocked_until DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rate_identifier (identifier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Data awal
-- ------------------------------------------------------------

-- Password: Admin123! (WAJIB diganti setelah login pertama)
INSERT IGNORE INTO admins (id, name, email, password_hash) VALUES
(1, 'Administrator', 'admin@pinjambuku.test', '$2y$10$Rp96jfxwg7C3Uy2AtB1LU.fxbmO4hrGnInziST2YVIY6bBrbJqKxW');

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('store_name', 'PinjamBuku'),
('store_tagline', 'Pinjam buku favoritmu, kapan saja.'),
('store_description', 'PinjamBuku adalah layanan peminjaman buku yang memudahkan kamu menemukan, memesan, dan meminjam buku tanpa ribet.'),
('store_address', 'Jl. Contoh No. 10, Yogyakarta'),
('store_phone', '0812-3456-7890'),
('store_email', 'halo@pinjambuku.test'),
('store_hours', 'Senin - Sabtu, 09.00 - 17.00 WIB'),
('fine_per_day', '2000'),
('min_loan_days', '3'),
('max_loan_days', '7'),
('pickup_deadline_days', '1');

INSERT IGNORE INTO categories (id, name, slug, description) VALUES
(1, 'Fiksi', 'fiksi', 'Novel, cerita pendek, dan karya fiksi lainnya.'),
(2, 'Non-Fiksi', 'non-fiksi', 'Buku pengetahuan, pengembangan diri, dan biografi.'),
(3, 'Anak', 'anak', 'Buku bacaan ramah anak dan remaja.');

INSERT IGNORE INTO books (id, category_id, title, slug, author, publisher, publish_year, isbn, description, cover_url, is_active) VALUES
(1, 1, 'Bumi Manusia', 'bumi-manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, '9789799731234', 'Kisah Minke, seorang pemuda terpelajar di akhir masa kolonial, menghadapi cinta, kelas sosial, dan kesadaran kebangsaan.', 'https://covers.openlibrary.org/b/isbn/9789799731234-L.jpg', 1),
(2, 3, 'Laskar Pelangi', 'laskar-pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '9789793062792', 'Sepuluh anak Belitung berjuang mendapatkan pendidikan dan meraih mimpi bersama Bu Mus.', 'https://covers.openlibrary.org/b/id/7079796-L.jpg', 1),
(3, 2, 'Sapiens: Riwayat Singkat Umat Manusia', 'sapiens', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', 2011, '9786024241797', 'Perjalanan umat manusia dari zaman batu hingga revolusi teknologi, ditinjau dari sejarah dan sains.', 'https://covers.openlibrary.org/b/id/8634250-L.jpg', 1),
(4, 2, 'Atomic Habits', 'atomic-habits', 'James Clear', 'Gramedia Pustaka Utama', 2019, '9786020633176', 'Panduan membangun kebiasaan kecil yang membawa perubahan besar dalam hidup.', 'https://covers.openlibrary.org/b/isbn/9786020633176-L.jpg', 1),
(5, 3, 'Bumi', 'bumi', 'Tere Liye', 'Gramedia Pustaka Utama', 2014, '9786020319298', 'Petualangan Raib dan sahabatnya menjelajahi dunia paralel yang penuh rahasia.', 'https://covers.openlibrary.org/b/id/12810708-L.jpg', 1),
(6, 2, 'Filosofi Teras', 'filosofi-teras', 'Henry Manampiring', 'Kompas', 2018, '9786024125189', 'Pengantar filsafat Stoisisme untuk menghadapi emosi dan masalah sehari-hari.', 'https://covers.openlibrary.org/b/id/15255806-L.jpg', 1);

INSERT IGNORE INTO book_variants (id, book_id, name, stock_total, stock_available, is_active) VALUES
(1, 1, 'Hardcover', 3, 3, 1),
(2, 1, 'Paperback', 5, 5, 1),
(3, 2, 'Paperback', 4, 4, 1),
(4, 3, 'Hardcover', 2, 2, 1),
(5, 3, 'Paperback', 4, 4, 1),
(6, 4, 'Paperback', 5, 5, 1),
(7, 5, 'Hardcover', 3, 3, 1),
(8, 5, 'Paperback', 4, 4, 1),
(9, 6, 'Paperback', 4, 4, 1);
