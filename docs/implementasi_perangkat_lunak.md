# 5.1 Implementasi Perangkat Lunak

Pendekatan berorientasi objek digunakan untuk membangun Website Wakaf Baiturrahman (Amanah Baiturrahman) ini, di mana modul dan atribut-atribut lainnya akan ditempatkan dalam kelas-kelas sebagai berikut:

## a. Model
Tabel berikut menjelaskan kelas-kelas Model Eloquent yang digunakan untuk memetakan dan mengakses tabel database:

### Tabel 5.1 Kelas Model (Eloquent)
| No. | Hasil Implementasi | Keterangan |
| :---: | :--- | :--- |
| 1. | `T01Role.php` | Kelas model yang digunakan untuk mengakses tabel peran (*roles*) pengguna pada database, mendefinisikan otoritas sistem. |
| 2. | `T02User.php` | Kelas model yang digunakan untuk mengakses tabel pengguna (*users*) pada database, baik untuk peran Nazhir maupun Wakif. |
| 3. | `T03ProgramWakaf.php` | Kelas model yang digunakan untuk mengakses tabel program wakaf (*program_wakaf*) pada database, menampung target dana dan tenggat waktu. |
| 4. | `T04Transaksi.php` | Kelas model yang digunakan untuk mengakses tabel transaksi (*transaksi*) pada database, menyimpan bukti transfer dan status pembayaran. |
| 5. | `T05LaporanPenyaluran.php` | Kelas model yang digunakan untuk mengakses tabel laporan penyaluran (*laporan_penyaluran*) dana wakaf oleh Nazhir pada database. |
| 6. | `User.php` | Kelas model autentikasi dasar bawaan Laravel yang terintegrasi dengan Fortify untuk kebutuhan keamanan sistem. |

---

## b. Controller
Tabel berikut menjelaskan kelas-kelas Controller yang berfungsi sebagai pengendali aliran data dan menjembatani frontend (Inertia/Vue) dengan backend:

### Tabel 5.2 Kelas Controller (Wakif & Nazhir)
| No. | Hasil Implementasi | Keterangan |
| :---: | :--- | :--- |
| 1. | `WakifAuthController.php` | Menangani proses autentikasi Wakif seperti masuk akun (*login*), pendaftaran (*register*), keluar akun (*logout*), dan pembaruan kata sandi. |
| 2. | `WakifDashboardController.php` | Menyediakan data statistik untuk halaman utama Wakif, daftar program aktif, detail program, riwayat donatur, dan tren wakaf tahunan. |
| 3. | `WakifTransaksiController.php` | Menangani logika input form donasi untuk tamu (*guest*) maupun pengguna terdaftar (*user*), serta menampilkan riwayat transaksi dan detail pembayaran. |
| 4. | `WakifUserController.php` | Mengatur pengambilan informasi profil pribadi Wakif serta menangani pembaruan (*update*) profil pengguna. |
| 5. | `NazhirAuthController.php` | Menangani otentikasi masuk dan keluar akun khusus untuk pengelola sistem (Nazhir). |
| 6. | `NazhirDashboardController.php` | Menyediakan data ringkasan total keuangan, jumlah transaksi, dan visualisasi grafik tren pada dasbor pengelolaan admin Nazhir. |
| 7. | `NazhirTransaksiController.php` | Menangani pengelolaan seluruh transaksi wakaf masuk, persetujuan (*approval*) pembayaran, serta ekspor data ke format CSV. |
| 8. | `NazhirProgramController.php` | Menangani manajemen data program wakaf (*create, read, update, delete*) serta proses unggah gambar sampul program. |
| 9. | `NazhirLaporanController.php` | Menangani manajemen data penyaluran dana wakaf kepada penerima manfaat (*create, read, update, delete* laporan). |
| 10. | `NazhirUserController.php` | Menyediakan data seluruh pengguna terdaftar dalam sistem untuk kebutuhan monitoring oleh Nazhir. |

---

## c. Service
Tabel berikut menjelaskan kelas-kelas Service yang bertugas mengelola logika bisnis (*business logic*) aplikasi sebelum disimpan ke database:

### Tabel 5.3 Kelas Service
| No. | Hasil Implementasi | Keterangan |
| :---: | :--- | :--- |
| 1. | `WakifAuthService.php` | Memproses logika registrasi akun baru, pencocokan kredensial login, dan pembuatan token otentikasi Sanctum untuk Wakif. |
| 2. | `WakifDashboardService.php` | Melakukan kalkulasi agregasi data statistik donatur, pencarian program wakaf, dan pemrosesan tren visual wakaf untuk Wakif. |
| 3. | `WakifTransaksiService.php` | Menangani pembuatan kode referensi transaksi (invoice), pemrosesan unggahan file bukti transfer, dan validasi minimal donasi. |
| 4. | `WakifUserService.php` | Memvalidasi dan memformat data perubahan profil pengguna sebelum diteruskan ke repositori penyimpanan. |
| 5. | `NazhirAuthService.php` | Mengelola verifikasi akun admin Nazhir dan pembuatan sesi kerja admin yang aman. |
| 6. | `NazhirDashboardService.php` | Mengatur kalkulasi total dana terkumpul, donatur terdaftar, dan data analisis grafik dasbor khusus Nazhir. |
| 7. | `NazhirTransaksiService.php` | Memproses perubahan status verifikasi transaksi wakaf (disetujui/ditolak) dan pemformatan data ekspor donatur. |
| 8. | `NazhirProgramService.php` | Melakukan validasi target dana, sisa waktu program, dan logika pengunggahan media visual program wakaf. |
| 9. | `NazhirLaporanService.php` | Mengelola validasi nominal dana yang disalurkan agar tidak melebihi total terkumpul, serta menyusun berita penyaluran. |

---

## d. Repository
Tabel berikut menjelaskan kelas-kelas Repository yang menangani kueri database secara langsung, memisahkan logika database dari logika bisnis (*decoupling*):

### Tabel 5.4 Kelas Repository
| No. | Hasil Implementasi | Keterangan |
| :---: | :--- | :--- |
| 1. | `WakifAuthRepository.php` | Menangani kueri database untuk pendaftaran pengguna, pemeriksaan email unik, dan penyimpanan token sesi Wakif. |
| 2. | `WakifDashboardRepository.php` | Mengambil data agregat dari tabel-tabel database untuk disajikan pada halaman visualisasi grafik wakif. |
| 3. | `WakifTransaksiRepository.php` | Menyimpan record transaksi donasi baru ke tabel `t04_transaksi` dan mengambil histori transaksi berdasarkan ID pengguna. |
| 4. | `WakifUserRepository.php` | Menangani pembaruan data record pengguna langsung ke dalam tabel `t02_users`. |
| 5. | `NazhirAuthRepository.php` | Mengakses data kredensial admin Nazhir pada tabel pengguna di database. |
| 6. | `NazhirDashboardRepository.php` | Menjalankan kueri database statistik keuangan, pertumbuhan donatur, dan persentase keberhasilan program wakaf. |
| 7. | `NazhirTransaksiRepository.php` | Melakukan pembaruan kolom status pembayaran pada tabel transaksi dan penarikan seluruh data donatur. |
| 8. | `NazhirProgramRepository.php` | Menangani kueri CRUD (*create, read, update, delete*) langsung ke tabel `t03_program_wakaf`. |
| 9. | `NazhirLaporanRepository.php` | Menangani kueri CRUD (*create, read, update, delete*) langsung ke tabel `t05_laporan_penyaluran`. |
