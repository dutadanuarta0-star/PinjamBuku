# TASKS.md

Status pengembangan aplikasi PinjamBuku. Tandai `[x]` bila sudah selesai dan diverifikasi manual.

## Fase 0 - Fondasi

- [x] Perbaiki `base_url` menjadi `http://localhost/PinjamBuku/`.
- [x] Konfigurasi database MySQL (`pinjambuku`, user `root`, tanpa password).
- [x] Autoload `database`, `session`, `form_validation`.
- [x] Aktifkan CSRF (`csrf_regenerate = FALSE` agar form ganda tetap valid).
- [x] Set zona waktu aplikasi ke `Asia/Jakarta`.
- [x] Amankan cookie session (httponly, same-site) dan aktifkan logging error.
- [x] Buat `database.sql` (skema, index, foreign key, data awal) yang aman diimpor ulang.
- [x] Tambah helper `pinjambuku_helper.php` (rupiah, label status, format tanggal, denda, pagination).
- [x] Tambah `MY_Controller` (settings, layout, guard auth, POST guard, sweep reservasi kedaluwarsa, analytics).

## Fase 1 - Storefront customer

- [x] Beranda: hero, statistik toko, kategori, buku terbaru, buku terpopuler.
- [x] Katalog: pencarian judul/penulis/ISBN, filter kategori, urutan, pagination.
- [x] Detail buku: info lengkap, stok per varian, pemilih varian + durasi 3-7 hari, buku serupa.
- [x] Halaman Tentang: profil toko, kontak, aturan peminjaman.
- [x] Registrasi & login customer dengan validasi server-side dan rate limit.
- [x] Logout customer (POST + CSRF).
- [x] Keranjang: tambah, ubah durasi, hapus, validasi stok/varian aktif.
- [x] Checkout multi-buku menjadi satu reservasi (stok langsung ditahan).
- [x] Halaman Peminjaman Saya: status, jatuh tempo, denda berjalan, pembatalan.
- [x] Banner peringatan header untuk buku terlambat dan denda belum dibayar.

## Fase 2 - Panel admin

- [x] Login admin terpisah dari session customer, rate limit, logout POST.
- [x] Dashboard: pengunjung hari ini/7 hari, reservasi menunggu, sedang dipinjam, buku terlambat, buku paling dilihat 30 hari.
- [x] CRUD kategori (slug otomatis unik, kategori dengan buku tidak bisa dihapus).
- [x] CRUD buku + varian/stok, cover URL, status aktif/nonaktif, filter & pencarian.
- [x] Proteksi stok: total tidak boleh di bawah jumlah dipinjam/direservasi, varian aktif tidak bisa dinonaktifkan saat masih dipakai.
- [x] Daftar & detail peminjaman dengan filter status dan pencarian kode/nama/email.
- [x] Konfirmasi setujui/tolak reservasi beserta alasan penolakan.
- [x] Tandai buku diambil (jatuh tempo dihitung dari tanggal ambil).
- [x] Tandai buku dikembalikan (denda final dihitung dan disimpan).
- [x] Tandai denda lunas.
- [x] Pengaturan toko & aturan pinjam (nama, tagline, deskripsi, kontak, jam buka, denda, durasi min/max, batas ambil).

## Fase 3 - Aturan domain

- [x] Durasi pinjam minimum 3 hari, maksimum 7 hari, divalidasi di server.
- [x] Stok ditahan sejak checkout sampai kembali/batal/tolak/kedaluwarsa.
- [x] Pengurangan stok atomik dengan transaksi + `UPDATE ... WHERE stock_available > 0`.
- [x] Jatuh tempo = tanggal ambil + (durasi - 1) hari.
- [x] Denda Rp2.000/hari/buku (dapat diubah admin) dihitung per hari kalender terlambat.
- [x] Reservasi yang tidak diambil sebelum `pickup_deadline` otomatis kedaluwarsa dan stok dikembalikan (sweep lazy tanpa cron).
- [x] Status reservasi: `pending`, `approved`, `borrowed`, `returned`, `rejected`, `cancelled`, `expired`.
- [x] Semua output di-escape; form POST memakai CSRF.

## Verifikasi manual (curl + Apache)

- [x] Semua halaman publik merespons 200 (beranda, katalog, detail, tentang, masuk, daftar).
- [x] Admin login dan semua halaman admin merespons 200.
- [x] Registrasi, login, logout, dan guard halaman customer.
- [x] Tambah/ubah/hapus keranjang dan checkout reservasi.
- [x] Setujui, tolak, ambil, kembali, dan denda lunas dari panel admin.
- [x] Pembatalan customer dan pengembalian stok.
- [x] Reservasi kedaluwarsa otomatis saat request berikutnya.
- [x] CRUD kategori dan CRUD buku/varian termasuk kasus stok tidak valid.
- [x] Validasi pengaturan toko dan aturan pinjam.
- [x] CSRF menolak POST tanpa token (403) dan guard menolak akses tanpa login.
- [x] `php -l` bersih untuk semua file `application/`.
- [x] Import ulang `database.sql` pada state bersih tanpa error log.

## Ditunda / belum dikerjakan

- [ ] Login Google OAuth (kolom `users.google_id` sudah disiapkan, alur manual dipakai sementara).
- [ ] Upload file cover (saat ini memakai URL gambar).
- [ ] Notifikasi email/WhatsApp saat reservasi disetujui atau jatuh tempo.
- [ ] Cron terjadwal untuk sweep kedaluwarsa (saat ini lazy per request).
- [ ] Automated test suite (tidak tersedia Composer/PHPUnit di lingkungan ini).
