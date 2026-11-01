# AGENTS.md

PinjamBuku: aplikasi peminjaman buku berbasis CodeIgniter 3.1.13 di XAMPP (Apache 2.4, PHP 8.0.30, MySQL/MariaDB). Storefront customer + panel admin, Tailwind lewat CDN, tanpa Composer library tambahan. Kode aplikasi ada di `application/`; jangan ubah `system/` (framework vendored).

## Menjalankan & memverifikasi

- Dilayani XAMPP Apache di <http://localhost/PinjamBuku/> (docroot `C:\xampp\htdocs`). Nyalakan Apache dan MySQL dari XAMPP Control Panel.
- `php`, `composer`, dan `phpunit` tidak ada di PATH. Gunakan `C:\xampp\php\php.exe` dan `C:\xampp\mysql\bin\mysql.exe`.
- Cek sintaks file yang diubah: `find application -name '*.php' -print0 | xargs -0 -n1 "C:\xampp\php\php.exe" -l`
- Verifikasi lewat Apache, bukan `php -S`: pretty URL bergantung pada mod_rewrite di `.htaccess` root (`index_page` kosong).
- Jangan jalankan `composer test:coverage`: `composer.json` masih manifes framework, folder `tests/` tidak ada, dan Composer tidak terpasang. Tidak ada test suite, linter, formatter, atau CI.
- Direktori ini bukan repo git (hanya ada `.gitignore`), jadi tidak ada riwayat commit.

## Database

- Nama database `pinjambuku` (utf8mb4), user `root`, password kosong. Konfigurasi di `application/config/database.php`.
- Import bersih:
	1. `"C:\xampp\mysql\bin\mysql.exe" -u root -e "DROP DATABASE IF EXISTS pinjambuku; CREATE DATABASE pinjambuku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"`
	2. `"C:\xampp\mysql\bin\mysql.exe" -u root pinjambuku < database.sql`
- `database.sql` memakai `CREATE TABLE IF NOT EXISTS` + `INSERT IGNORE`, jadi aman diimpor ulang tanpa menghapus data baru.
- Tabel: `admins`, `users`, `categories`, `books`, `book_variants`, `cart_items`, `loans`, `loan_items`, `settings`, `page_views`, `rate_limits`.
- Admin awal: `admin@pinjambuku.test` / `Admin123!`. Ganti sebelum dipakai di luar lokal.
- Semua waktu memakai `Asia/Jakarta` (dipaksa di `MY_Controller`), MySQL diimpor dengan `SET time_zone = '+07:00'`.

## Arsitektur

- `application/core/MY_Controller.php`: memuat database/session/helper/model umum, membaca `settings`, menyimpan `currentUser`/`currentAdmin`, menghitung badge keranjang dan peringatan customer, menjalankan sweep reservasi kedaluwarsa, lalu menyediakan `renderPublic()`, `renderAdmin()`, guard `requireUser()`/`requireAdmin()`, dan `requirePost()`.
- Controller publik: `Home`, `Catalog`, `Auth`, `Cart`, `Loans`. Controller admin: `AdminAuth`, `AdminDashboard`, `AdminCategories`, `AdminBooks`, `AdminLoans`, `AdminSettings`.
- Model: `SettingModel`, `UserModel`, `AdminModel`, `CategoryModel`, `BookModel`, `VariantModel`, `CartModel`, `LoanModel`, `AnalyticsModel`, `RateLimitModel`.
- View: `application/views/public/`, `application/views/admin/`, layout di `application/views/layouts/`.
- Helper `pinjambuku_helper.php`: `rupiah()`, `loan_status_label()`, `loan_status_class()`, `format_datetime()`, `days_late()`, `is_valid_cover_url()`, `pagination_config()`.
- Route lengkap ada di `application/config/routes.php`; default controller `home`. CI3 hanya mendukung wildcard `:any` dan `:num` (bukan `:segment`).
- Pagination memakai query string `?page=N` dengan `reuse_query_string`; view menerima `$pagination`.

## Aturan domain

- Durasi pinjam 3-7 hari per buku, batas disimpan di settings (`min_loan_days`, `max_loan_days`) dan divalidasi di server.
- Checkout menahan stok saat itu juga. Pengurangan stok atomik lewat transaksi dan `UPDATE ... WHERE stock_available > 0`.
- Status reservasi: `pending` -> `approved` -> `borrowed` -> `returned`, plus `rejected`, `cancelled`, `expired`.
- Stok kembali saat buku dikembalikan, reservasi ditolak/dibatalkan, atau kedaluwarsa.
- Jatuh tempo tiap item = tanggal ambil + (durasi - 1) hari.
- Denda = jumlah hari kalender terlambat x `fine_per_day` (default Rp2.000) per buku; disimpan saat pengembalian, lalu admin dapat menandai lunas.
- Reservasi yang tidak diambil sampai `pickup_deadline` dibersihkan secara lazy pada request berikutnya (tidak ada cron di XAMPP).
- Varian = format/edisi fisik dengan stok sendiri. Stok total tidak boleh turun di bawah jumlah yang sedang dipinjam/direservasi; varian yang masih dipakai tidak bisa dinonaktifkan.

## Keamanan

- CSRF aktif (`csrf_protection = TRUE`, `csrf_regenerate = FALSE`); semua POST harus memakai `form_open()`/token.
- Session customer (`user`) dan admin (`admin`) terpisah dalam satu session CI. Logout hanya lewat POST.
- Rate limit login/registrasi: maksimal 5 percobaan per 15 menit per IP + email (`RateLimitModel`).
- Password memakai `password_hash()`/`password_verify()`. Kolom `users.google_id` disiapkan untuk OAuth yang belum diaktifkan.
- Escape semua output dinamis dengan `html_escape()` (atau cast `(int)`/`(float)` untuk angka). Jangan menampilkan input mentah.
- Cover berupa URL `http(s)` tervalidasi; jika kosong/tidak valid gunakan `assets/img/placeholder.svg`.
- Root `.htaccess` memblokir dotfiles dan `*.md|json|sql|log|...`, jadi jangan menaruh data statis di webroot; sajikan lewat controller.
- Tidak ada `.env`; seluruh konfigurasi di `application/config/*.php`. Environment default `development` kecuali Apache menyetel `CI_ENV`.

## Konvensi

- Gaya CodeIgniter 3 tanpa namespace: controller `application/controllers/Foo.php` (`class Foo extends CI_Controller`), model extends `CI_Model`, konfigurasi berupa array `$config[...]`.
- Nama controller PascalCase, method/variabel camelCase, tabel/kolom database snake_case.
- Gunakan Query Builder, validasi server-side lewat `form_validation`, dan transaksi database untuk perubahan stok.
- Runtime PHP 8.0: hindari sintaks 8.1+ (enum, readonly property, `never`, first-class callable syntax).
- Gunakan tab, LF, UTF-8, dan newline di akhir file (`.editorconfig`).
- Komentar hanya untuk logika yang tidak jelas (transaksi stok, sweep kedaluwarsa, perhitungan denda).
- Catat progres di `TASKS.md`; perbarui `AGENTS.md` hanya untuk keputusan arsitektur/konvensi penting.
