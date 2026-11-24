# LMS ASJ — SMKN 1 Kepulauan Selayar

Aplikasi pembelajaran PHP Native MVC sederhana dengan MySQL/MariaDB, PDO, AJAX, tiga role (admin/guru/siswa).

## Kebutuhan
- PHP 8.1+ dengan ekstensi `pdo_mysql`, `fileinfo`
- MySQL 8 / MariaDB 10.5+
- Apache (XAMPP/Laragon) atau PHP built-in server

## Instalasi
1. Salin folder `lms-asj` ke `htdocs` (XAMPP) atau lokasi web server.
2. Buat database `lms_asj`.
3. Import `database/schema.sql` melalui phpMyAdmin atau CLI.
4. Sesuaikan kredensial pada `config/config.php`.
5. Buka `http://localhost/lms-asj/install.php` satu kali untuk membuat akun demo dan data awal.
6. Setelah instalasi berhasil, hapus `install.php` dari server. Jangan biarkan installer tersedia untuk umum.
7. Buka `http://localhost/lms-asj/`.

## Akun demo
- Admin: username `198501012010011001`, password `admin123`
- Guru: username `198701012010011001`, password `guru123`
- Siswa: username `0061234567`, password `siswa123`

Password di-hash dengan `password_hash()`. Ganti password awal setelah login pertama dan sebelum penggunaan nyata.

## Catatan konfigurasi
- Jika aplikasi dipasang di subfolder selain `/lms-asj`, ubah `BASE_URL` pada `config/config.php`.
- Folder `storage/uploads` harus dapat ditulis oleh PHP.
- Untuk produksi, aktifkan HTTPS, nonaktifkan display_errors, batasi ukuran upload pada php.ini, dan hapus install.php.
- File upload disimpan dengan nama acak dan diperiksa MIME-nya. Tambahkan antivirus/malware scanning sebelum penggunaan publik.

## Fitur yang tersedia
- Login/logout, session, CSRF token, pembatasan akses berdasarkan role.
- Dashboard terpisah admin, guru, siswa.
- Admin: CRUD pengguna, kelas, mata pelajaran, penugasan guru ke kelas/mapel.
- Guru: modul dan materi, tugas, melihat pengumpulan, memberi nilai/umpan balik, absensi.
- Siswa: melihat modul/materi, mengumpulkan tugas, melihat nilai dan absensi.
- AJAX untuk pencarian daftar pengguna dan pengiriman formulir penilaian/pengumpulan/absensi.
- Antarmuka responsif, pencarian, notifikasi, dan grafik ringkas.

Beberapa fungsi lanjutan seperti kuis otomatis, forum real-time, sertifikat PDF, email reset password, dan integrasi SSO belum termasuk pada rilis fondasi ini.
