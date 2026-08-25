# 🕌 Amanah Baiturrahman - Sistem Informasi Pengelolaan & Penyaluran Dana Wakaf

Amanah Baiturrahman adalah platform web modern untuk manajemen, transparansi, dan penyaluran dana wakaf berbasis **Laravel 11**, **Vue 3 (Inertia.js)**, dan **Tailwind CSS**. Platform ini memfasilitasi donatur (Wakif), pengelola (Nazhir), dan pengawas (Superadmin) dalam mengelola program wakaf secara realtime dan akuntabel.

---

## 🚀 Fitur Utama

- **Wakif (Donatur)**:
  - Katalog & Detail Program Wakaf dengan indikator target & persentase terkumpul.
  - Grafik Trend Wakaf harian/bulanan & Penyebaran Program interaktif.
  - Simulasi Pembayaran Wakaf (QRIS / Bukti Transfer) & Riwayat Transaksi.
  - Laporan transparansi penyaluran manfaat secara realtime.

- **Nazhir (Pengelola/Admin)**:
  - Dashboard Analytics Realtime (Total Dana, Jumlah Donatur, Penerima Manfaat).
  - Persetujuan & Verifikasi Transaksi Masuk (Approve / Reject).
  - Manajemen Program Wakaf (Tambah, Edit, Hapus, Upload Thumbnail/Deskripsi Rich Text).
  - Pelaporan Penyaluran Dana & Pengajuan Pencairan Dana.

- **Superadmin (Pengawas System)**:
  - Persetujuan Akun Nazhir baru & Manajemen Pengguna.
  - Verifikasi Program Wakaf baru sebelum dipublikasikan.
  - Verifikasi Pengajuan Pencairan Dana Nazhir & Unggah Surat Bukti Pencairan.

---

## 🛠️ Arsitektur & Teknologi

- **Backend**: PHP 8.2+ / Laravel 11 Framework
- **Frontend**: Vue 3 (Composition API / Script Setup), TypeScript, Inertia.js
- **Styling**: Tailwind CSS
- **Build Tool**: Vite
- **Database**: MySQL 8.0+ / MariaDB / SQLite
- **Autentikasi**: Laravel Sanctum (Inertia Session & Token Bearer API)

---

## 📋 Persyaratan Sistem (Prerequisites)

Pastikan perangkat Anda sudah terinstall:
- **PHP**: `>= 8.2` (dengan ekstensi: `pdo`, `pdo_mysql` / `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- **Composer**: `>= 2.x`
- **Node.js**: `>= 18.x` (disarankan Node.js LTS v20+) & **NPM**
- **Database**: MySQL / MariaDB (via Laragon / XAMPP) atau SQLite
- **Git**

---

## 💻 Panduan Instalasi & Setup dari Awal

Ikuti langkah-langkah berikut untuk menjalankan project di lingkungan lokal:

### 1. Clone Repository
```bash
git clone https://github.com/queenxhr/amanah-baiturrahman.git
cd amanah-baiturrahman
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file konfigurasi lingkungan dari contoh `.env.example`:
```bash
cp .env.example .env
```
Generate Application Key Laravel:
```bash
php artisan key:generate
```

### 5. Pembuatan Database
Pilih salah satu metode database di bawah ini:

#### **Opsi A: MySQL / MariaDB (Rekomendasi - Laragon/XAMPP)**
1. Buka MySQL Client (phpMyAdmin / HeidiSQL / Laragon).
2. Buat database baru bernama `amanah_baiturrahman`:
   ```sql
   CREATE DATABASE amanah_baiturrahman CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Sesuaikan baris berikut pada file `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=amanah_baiturrahman
   DB_USERNAME=root
   DB_PASSWORD=
   ```

#### **Opsi B: SQLite (Alternatif Tanpa Server Database)**
1. Buat file `database.sqlite` di folder `database/`:
   ```bash
   touch database/database.sqlite
   ```
2. Sesuaikan baris berikut pada file `.env`:
   ```ini
   DB_CONNECTION=sqlite
   ```

### 6. Migrasi Database & Seeder Data Awal
Jalankan migrasi tabel dan pengisian data sampel (*seeders*):
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symbolic Link Storage Public
Buat tautan simbolik dari folder `storage/app/public` ke `public/storage` agar file gambar program dan bukti transfer dapat diakses oleh browser:
```bash
php artisan storage:link
```

### 8. Jalankan Server Pengembangan (Dev Server)

Jalankan dua terminal secara bersamaan:

- **Terminal 1 (Vite Frontend Watcher)**:
  ```bash
  npm run dev
  ```

- **Terminal 2 (Laravel Backend Server)**:
  ```bash
  php artisan serve
  ```

Aplikasi sekarang dapat diakses melalui browser di: `http://127.0.0.1:8000`.

---

## 🔑 Kredensial Akun Default (Seeder)

Setelah menjalankan `php artisan migrate:fresh --seed`, Anda dapat menguji login menggunakan akun sampel berikut:

| Peran (Role) | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin@baiturrahman.com` | `password` | Verifikasi Nazhir, Program, Pencairan |
| **Nazhir** | `nazhir@baiturrahman.com` | `password` | Kelola Program, Transaksi, Laporan |
| **Wakif** | `wakif@baiturrahman.com` | `password` | Berdonasi, Lihat Grafik & Transparansi |

---

## 🧪 Menjalankan Pengujian (Automated Tests)

Project ini dilengkapi dengan pengujian *Feature & Unit Tests* (PHPUnit & Pest):

```bash
php artisan test
```

---

## 📁 Struktur Folder Utama

```text
amanah-baiturrahman/
├── app/
│   ├── Http/Controllers/    # Controllers (Wakif, Nazhir, Superadmin)
│   ├── Models/              # Eloquent Models (T01-T05 & User)
│   ├── Repositories/        # Design Pattern Repository Implementation
│   └── Services/            # Business Logic Layer Services
├── config/                  # Konfigurasi aplikasi & database
├── database/
│   ├── migrations/          # Schema tabel database
│   └── seeders/             # Data sampel & testing seeder
├── docs/                    # Dokumentasi API & Diagram Sistem
├── public/                  # Public assets & entry point index.php
├── resources/
│   ├── js/
│   │   ├── components/      # UI Components Vue
│   │   ├── layouts/         # Layout (Wakif, Nazhir, Superadmin)
│   │   └── pages/           # Halaman Vue 3 Inertia
│   └── views/               # Blade root template (app.blade.php)
├── routes/
│   ├── api.php              # Endpoint API Backend (Sanctum)
│   └── web.php              # Route Halaman Inertia Frontend
└── vite.config.ts           # Konfigurasi Bundler Vite
```

---

## 📄 Lisensi

Proyek ini dibuat untuk sistem informasi penyaluran dana wakaf Masjid Baiturrahman. All rights reserved.