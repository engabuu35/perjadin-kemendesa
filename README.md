<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## SIPERDIN - Sistem Informasi Perjalanan Dinas
Sistem manajemen perjalanan dinas terintegrasi untuk mengelola pengajuan surat tugas, pelaporan kegiatan harian (geotagging), hingga verifikasi keuangan secara digital.

## Tentang Proyek
Aplikasi ini dibangun menggunakan Laravel untuk memfasilitasi proses administrasi perjalanan dinas di lingkungan Inspektorat Jenderal (atau instansi terkait). Sistem ini menghubungkan 4 aktor utama:
- Pegawai: Pelaksana tugas yang melakukan perjalanan.
- PIC (Person In Charge): Admin wilayah yang membuat surat tugas dan menyusun laporan keuangan.
- PPK (Pejabat Pembuat Komitmen): Verifikator akhir pembayaran keuangan.
- Pimpinan: Pihak yang memantau dan menyetujui surat tugas.

## Fitur Utama
1. Pegawai 
    - Melihat jadwal penugasan aktif di Dashboard.
    - Melakukan Geotagging Harian (Absensi Lokasi) saat bertugas.
    - Mengisi Uraian Kegiatan harian (minimal 100 kata).
    - Menyelesaikan tugas secara mandiri via sistem.
2. PIC
    - Manajemen Pegawai: Menambah, edit, dan delete.
    - Manajemen Surat Tugas: Membuat surat tugas baru dan menentukan tim (Ketua & Anggota).
    - Input Keuangan Massal (Bulk): Menginput rincian biaya (Tiket, Hotel, Uang Harian) untuk seluruh tim dalam satu tampilan tabel.
    - Upload Bukti: Mengunggah bukti tiket/hotel per item.
    - Revisi: Memperbaiki laporan yang dikembalikan oleh PPK.
3. PPK 
    - Verifikasi Berjenjang: Memeriksa kelengkapan bukti dan kewajaran biaya.
    - Approval/Rejection: Menyetujui pembayaran atau menolak dengan catatan revisi.
    - Input SPM/SP2D: Menginput nomor dokumen pencairan dana.
    - Rekapitulasi: Melihat dan mengunduh rekapitulasi perjalanan dinas tahunan (Excel).
4. Pimpinan
    - Melakukan Monitoring perjalanan dinas pegawai

## Alur Status Data
Sistem menggunakan tabel statusperjadin sebagai gerbang logika utama.

1. Belum Berlangsung:
    - Surat tugas baru dibuat oleh PIC.
    - Muncul di Beranda Pegawai (warna Biru).
2. Sedang Berlangsung
    - Tanggal hari ini masuk dalam periode tugas.
    - Pegawai bisa melakukan Geotagging.
3. Pembuatan Laporan (PIC)
    - Semua pegawai dalam tim sudah klik "Selesai".
    - Data muncul di Dashboard PIC dengan label "Perlu Tindakan".
    - PIC mulai input biaya & upload bukti.
4. Menunggu Validasi PPK
    - PIC sudah mengirim laporan ke PPK.
    - Data muncul di Dashboard PPK (label "Butuh Validasi").
5. Perlu Revisi
    - PPK menolak laporan.
    - Data kembali ke Dashboard PIC dengan catatan revisi.
6. Selesai
    - PPK menyetujui dan menginput nomor SP2D.
    - Data masuk ke menu Riwayat / Rekapitulasi.
7. Diselesaikan Manual
    - PIC menyelesaikan manual perjadin dari menu manajemen perjalanan dinas
8. Dibatalkan
    - PIC membatalkan perjadin dari menu manajemen perjalanan dinas

## Struktur Folder & File Penting

Berikut adalah peta lokasi file-file kunci dalam proyek ini agar mudah dipelajari:

1. Controller(app/Http/Controllers/)
    - Auth_Controller: Menangani proses login dan logout user.
    - Beranda_Controller: Menampilkan halaman dashboard utama untuk pegawai.
    - Checkin_Controller: Mengatur fitur absensi lokasi (geotagging) harian.
    - Controller: Base controller induk untuk semua controller lain.
    - LaporanKeuangan_Controller: Mengelola data detail tabel laporan keuangan (CRUD).
    - Location_Controller: Menyediakan data koordinat peta untuk fitur map.
    - LSRampung_Controller: Menangani cetak dan rekap dokumen LS Rampung.
    - ManagePegawai_Controller: Mengelola data master pegawai (tambah/edit/hapus).
    - Notification_Controller: Mengirim notifikasi email/sistem terkait status tugas.
    - PasswordReset_Controller: Menangani proses reset password jika user lupa.
    - Pelaporan_Controller: (PIC) Mengelola input biaya massal dan pengiriman ke PPK.
    - Perjadin_Controller: (Pegawai) Mengatur aktivitas harian dan penyelesaian tugas individu.
    - PerjadinTambah_Controller: (PIC) Membuat surat tugas baru dan menentukan tim.
    - Pimpinan_Controller: Dashboard monitoring khusus untuk role Pimpinan.
    - PPK_Controller: (PPK) Memverifikasi laporan, menyetujui bayar, atau menolak.
    - Profile_Controller: Mengelola edit profil dan ganti password user sendiri.
    - Riwayat_Controller: Menampilkan arsip perjalanan dinas yang sudah selesai.

2. Views (resources/views/)
    - auth: Folder untuk autentifikasi seperti login, forget pass, reset pass.
    - components: Folder untuk menyimpan file yang berisi potongan-potongan syntax yang bisa digunakan lagi.
    - emails: Folder untuk menangani tampilan notifikasi email.
    - layouts: Folder yang berisi file-file layout per role.
    - pages: Folder untuk file yang digunakan di semua user.
    - partial: Folder yang berisi file-file pembentuk layouts.
    - pic: Folder untuk meyimpan semua file menu di PIC.
    - pimpinan: Folder untuk meyimpan semua file menu di pimpinan.
    - ppk: Folder untuk meyimpan semua file menu di ppk.

## Instalasi & Setup

Clone Repositori:
git clone [https://github.com/username/siperdin.git](https://github.com/username/siperdin.git)
cd siperdin

Install Dependencies:
composer install
npm install

Konfigurasi Environment Duplikasi file .env.example menjadi .env dan sesuaikan database:
cp .env.example .env
php artisan key:generate

Migrasi & Seeding Database, penting untuk menjalankan seeder agar tabel master (Status, Role, Pangkat) terisi:
php artisan migrate:fresh --seed --class=DatabaseSeeder


Gunakan PerjadinDataSeeder jika ingin data dummy transaksi. Jalankan Server:
php artisan serve

Buka http://localhost:8000 di browser.

Catatan Pengembang:
- Pastikan folder storage/app/public sudah di-link ke public/storage dengan perintah "php artisan storage:link" agar file bukti bisa diakses.
- Untuk notifikasi pastikan menjalankan "php artisan queue:work".
