# Dokumentasi Endpoint API Backend - Amanah Baiturrahman

Dokumentasi ini menjelaskan secara lengkap seluruh endpoint API yang tersedia pada sistem backend Amanah Baiturrahman, baik untuk sisi **Wakif (Donatur)** maupun **Nazhir (Pengelola/Admin)**.

---

## Informasi Umum
- **Base URL Lokal**: `http://127.0.0.1:8000` (atau port server Laravel yang Anda jalankan).
- **Format Payload**: Seluruh request dan response menggunakan format **JSON** (`Content-Type: application/json`), kecuali untuk endpoint transaksi yang mengunggah file bukti pembayaran menggunakan format **Multipart/Form-Data**.
- **Autentikasi**: Menggunakan **Laravel Sanctum** (token bearer). Untuk endpoint berlabel `Auth Required: Yes`, sertakan header berikut:
  ```http
  Authorization: Bearer <your_access_token>
  ```

---

## 1. KELOMPOK ENDPOINT: WAKIF (DONATUR)
Seluruh endpoint Wakif memiliki prefix `/api/wakif`.

### A. Autentikasi & Pengelolaan Profil

#### 1. Pendaftaran Akun (Sign Up)
- **Method & URL**: `POST` `/api/wakif/signup`
- **Auth Required**: No
- **Request Body (JSON)**:
  ```json
  {
    "nama": "Farhan Wakif",
    "email": "farhan@example.com",
    "no_hp": "081234567890",
    "password": "Password123!"
  }
  ```
- **Response Sukses (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Register successful",
    "data": {
      "user": {
        "id_user": 12,
        "nama": "Farhan Wakif",
        "email": "farhan@example.com",
        "no_hp": "081234567890",
        "id_role": 2
      },
      "token": "1|abcdef123456..."
    }
  }
  ```

#### 2. Masuk Akun (Login)
- **Method & URL**: `POST` `/api/wakif/login`
- **Auth Required**: No
- **Request Body (JSON)**:
  ```json
  {
    "email": "farhan@example.com",
    "password": "Password123!"
  }
  ```
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {
        "id_user": 12,
        "nama": "Farhan Wakif",
        "email": "farhan@example.com",
        "no_hp": "081234567890",
        "id_role": 2
      },
      "token": "2|ghijk7890..."
    }
  }
  ```

#### 3. Keluar Akun (Logout)
- **Method & URL**: `POST` `/api/wakif/logout`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Logout successful"
  }
  ```

#### 4. Ubah Sandi (Update Password)
- **Method & URL**: `POST` `/api/wakif/ubah-password`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "old_password": "Password123!",
    "new_password": "NewPassword123!"
  }
  ```
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Password updated successful"
  }
  ```

#### 5. Ambil Data Profil (Get Profile)
- **Method & URL**: `GET` `/api/wakif/user`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id_user": 12,
      "nama": "Farhan Wakif",
      "email": "farhan@example.com",
      "no_hp": "081234567890",
      "jenis_kelamin": "L",
      "alamat": "Bandung, Jawa Barat",
      "tanggal_lahir": "1998-05-20"
    }
  }
  ```

#### 6. Perbarui Data Profil (Update Profile)
- **Method & URL**: `PUT` `/api/wakif/user`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "nama": "Farhan Wakif Update",
    "email": "farhan_new@example.com",
    "no_hp": "081234567899",
    "jenis_kelamin": "L",
    "alamat": "Bandung Kota, Jawa Barat",
    "tanggal_lahir": "1998-05-20"
  }
  ```
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Profile updated successfully"
  }
  ```

---

### B. Dashboard & Informasi Program (Public / Tanpa Login)

#### 1. Counter Agregat Statistik Utama
Mengambil data ringkasan total program, dana terkumpul, donatur, dan penerima manfaat.
- **Method & URL**: `GET` `/api/wakif/counter`
- **Parameter Query (Opsional)**:
  - `bulan` (integer, e.g. `6`)
  - `tahun` (integer, e.g. `2026`)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "program": 5,
      "wakaf_terkumpul": 152500000,
      "donatur": 87,
      "penerima_manfaat": 250
    }
  }
  ```

#### 2. Daftar Program Wakaf Aktif
- **Method & URL**: `GET` `/api/wakif/program-wakaf`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_program": 1,
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "deskripsi": "Program wakaf air bersih...",
        "target_dana": 25000000,
        "dana_terkumpul": 12500000,
        "due_date": "2026-12-31T00:00:00Z",
        "gambar_thumbnail": "/storage/programs/sumur.jpg",
        "status_program": 1
      }
    ]
  }
  ```

#### 3. Detail Program Berdasarkan ID
- **Method & URL**: `GET` `/api/wakif/program-wakaf/{id}`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id_program": 1,
      "nama_program": "Wakaf Sumur Bor Ponpes",
      "deskripsi": "...",
      "target_dana": 25000000,
      "dana_terkumpul": 12500000,
      "due_date": "2026-12-31T00:00:00Z"
    }
  }
  ```

#### 4. Deskripsi Lengkap Program
- **Method & URL**: `GET` `/api/wakif/program-wakaf/{id}/deskripsi`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "deskripsi": "<p>Penjelasan lengkap program dalam format HTML dari editor...</p>"
    }
  }
  ```

#### 5. Sisa Waktu Program (Countdown)
- **Method & URL**: `GET` `/api/wakif/program-wakaf/{id}/countdown`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "remaining_days": 182
    }
  }
  ```

#### 6. Total Akumulasi Donatur Sistem
- **Method & URL**: `GET` `/api/wakif/counter-donatur`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "total_donatur": 87
    }
  }
  ```

#### 7. Daftar Riwayat Donatur Per Program
- **Method & URL**: `GET` `/api/wakif/program-wakaf/{id}/donatur`
- **Parameter Query (Opsional)**:
  - `start` (date format `YYYY-MM-DD`)
  - `end` (date format `YYYY-MM-DD`)
  - `limit` (integer)
  - `page` (integer)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "nama": "Hamba Allah",
        "nominal": 100000,
        "tanggal": "2026-06-11T12:00:00Z",
        "pesan_doa": "Semoga berkah"
      }
    ]
  }
  ```

#### 8. Berita & Laporan Penyaluran Wakaf
- **Method & URL**: `GET` `/api/wakif/berita-laporan`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_laporan": 1,
        "judul_laporan": "Penyaluran Pompa Sumur Bor Ponpes",
        "keterangan": "Pemasangan pipa paralon dan pompa air berhasil diselesaikan...",
        "dana_disalurkan": 5000000,
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "tanggal_laporan": "2026-06-10T15:30:00Z"
      }
    ]
  }
  ```

#### 9. Persentase Penyebaran Wakaf Terhadap Program
- **Method & URL**: `GET` `/api/wakif/penyebaran-program`
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "persen": 45
      },
      {
        "nama_program": "Wakaf Al-Quran Santri Yatim",
        "persen": 55
      }
    ]
  }
  ```

#### 10. Grafik Trend Pengumpulan Wakaf Tahunan
- **Method & URL**: `GET` `/api/wakif/trend-wakaf`
- **Parameter Query (Wajib)**:
  - `tahun` (integer, e.g. `2026`)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      { "bulan": 1, "wakaf_terkumpul": 15000000 },
      { "bulan": 2, "wakaf_terkumpul": 22000000 },
      { "bulan": 6, "wakaf_terkumpul": 45000000 }
    ]
  }
  ```

---

### C. Transaksi Wakaf (Pembayaran & Histori)

#### 1. Transaksi Tanpa Login (Guest)
- **Method & URL**: `POST` `/api/wakif/transaksi/guest`
- **Auth Required**: No
- **Request Body (Multipart/Form-Data)**:
  - `nama` (string, e.g. `"Hamba Allah"`)
  - `id_program` (integer, e.g. `1`)
  - `nominal` (integer, e.g. `150000`)
  - `pesan_doa` (string/opsional)
  - `bukti_pembayaran` (file binary gambar, e.g. JPG/PNG)
- **Response Sukses (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil disimpan, menunggu verifikasi Nazhir",
    "data": {
      "id_transaksi": 45,
      "nama_wakif": "Hamba Allah",
      "nominal": 150000
    }
  }
  ```

#### 2. Transaksi Dengan Login (User)
- **Method & URL**: `POST` `/api/wakif/transaksi/user`
- **Auth Required**: Yes
- **Request Body (Multipart/Form-Data)**:
  - `nama` (string, e.g. `"Farhan Wakif"`)
  - `id_program` (integer, e.g. `1`)
  - `nominal` (integer, e.g. `250000`)
  - `pesan_doa` (string/opsional)
  - `bukti_pembayaran` (file binary gambar)
- **Response Sukses (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil disimpan, silakan tunggu verifikasi Nazhir",
    "data": {
      "id_transaksi": 46,
      "nama_wakif": "Farhan Wakif",
      "nominal": 250000
    }
  }
  ```

#### 3. List Riwayat Transaksi Wakif Login
Mengambil seluruh histori transaksi pengguna yang sedang login.
- **Method & URL**: `GET` `/api/wakif/transaksi/riwayat`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_transaksi": 46,
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "nominal": 250000,
        "tanggal": "2026-06-11T12:30:00Z",
        "status_pembayaran": 1, 
        "bukti_pembayaran": "/storage/bukti/abc.jpg"
      }
    ]
  }
  ```
  *(Catatan status_pembayaran: `0` = Pending, `1` = Approved/Sukses, `2` = Rejected/Ditolak)*

#### 4. Detail Transaksi Berdasarkan ID
- **Method & URL**: `GET` `/api/wakif/transaksi/{id}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id_transaksi": 46,
      "nama_program": "Wakaf Sumur Bor Ponpes",
      "nominal": 250000,
      "status_pembayaran": 1,
      "tanggal_transaksi": "2026-06-11 12:30:00",
      "pesan_doa": "Semoga menjadi pahala mengalir"
    }
  }
  ```

#### 5. Detail Instruksi Pembayaran
- **Method & URL**: `GET` `/api/wakif/detail-pembayaran/{id}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "nominal": 250000,
      "id_transaksi": 46,
      "qris_image": "/qris.png",
      "rekening_transfer": "Bank Syariah Indonesia (BSI) - 7722334455 (a.n Baiturrahman)"
    }
  }
  ```

---

## 2. KELOMPOK ENDPOINT: NAZHIR (ADMIN/PENGELOLA)
Seluruh endpoint Nazhir memiliki prefix `/api/nazhir`.

### A. Autentikasi

#### 1. Masuk Akun Nazhir
- **Method & URL**: `POST` `/api/nazhir/login`
- **Auth Required**: No
- **Request Body (JSON)**:
  ```json
  {
    "email": "nazhir@example.com",
    "password": "nazhirpassword"
  }
  ```
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {
        "id_user": 1,
        "nama": "Administrator Nazhir",
        "email": "nazhir@example.com",
        "id_role": 1
      },
      "token": "3|admin_token_xyz..."
    }
  }
  ```

#### 2. Keluar Akun Nazhir
- **Method & URL**: `POST` `/api/nazhir/logout`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Logout successful"
  }
  ```

---

### B. Manajemen User & Dashboard

#### 1. Daftar Seluruh User Wakif (Donatur Terdaftar)
Mengambil daftar akun ber-role Wakif (id_role: 2) untuk tabel Manajemen User.
- **Method & URL**: `GET` `/api/nazhir/users`
- **Auth Required**: Yes
- **Parameter Query (Opsional)**:
  - `search` (string, mencarian berdasarkan nama/email/no_hp)
  - `sort` (string, `asc` atau `desc` berdasarkan tanggal daftar)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_user": 12,
        "nama": "Farhan Wakif",
        "email": "farhan@example.com",
        "no_hp": "081234567890",
        "jenis_kelamin": "L",
        "alamat": "Bandung",
        "created_at": "2026-06-01 10:00:00",
        "total_wakaf": 1500000
      }
    ]
  }
  ```

#### 2. Counter Ringkasan Statistik Finansial
- **Method & URL**: `GET` `/api/nazhir/counter`
- **Auth Required**: Yes
- **Parameter Query (Opsional)**:
  - `start` (date format `YYYY-MM-DD`)
  - `end` (date format `YYYY-MM-DD`)
  - `program` (integer ID Program)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "total_wakaf": 125000000,
      "total_donatur": 87,
      "total_program": 5,
      "dana_disalurkan": 45000000
    }
  }
  ```

#### 3. Persentase Distribusi Wakaf
- **Method & URL**: `GET` `/api/nazhir/penyebaran-program`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      { "nama_program": "Wakaf Sumur Bor Ponpes", "persen": 45 }
    ]
  }
  ```

#### 4. Trend Wakaf Tahunan Nazhir
- **Method & URL**: `GET` `/api/nazhir/trend-wakaf`
- **Auth Required**: Yes
- **Parameter Query (Wajib)**:
  - `tahun` (integer, e.g. `2026`)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      { "bulan": 6, "wakaf_terkumpul": 45000000 }
    ]
  }
  ```

---

### C. Manajemen Transaksi Wakif

#### 1. List Transaksi Wakif
Mengambil daftar pengajuan pembayaran dari Wakif (Guest & User).
- **Method & URL**: `GET` `/api/nazhir/transaksi`
- **Auth Required**: Yes
- **Parameter Query (Opsional)**:
  - `start` (date format `YYYY-MM-DD`)
  - `end` (date format `YYYY-MM-DD`)
  - `limit` (integer)
  - `page` (integer)
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_transaksi": 46,
        "nama_wakif": "Farhan Wakif",
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "nominal": 250000,
        "tanggal": "2026-06-11 12:30:00",
        "status_pembayaran": 0
      }
    ]
  }
  ```

#### 2. Unduh Data Transaksi (Export CSV)
Mendapatkan file CSV berisi semua data transaksi.
- **Method & URL**: `GET` `/api/nazhir/transaksi/export-csv`
- **Auth Required**: Yes
- **Response**: File download stream (`text/csv`).

#### 3. Lihat File Bukti Pembayaran
Mengambil file bukti pembayaran yang diunggah oleh donatur.
- **Method & URL**: `GET` `/api/nazhir/transaksi/{id}/bukti`
- **Auth Required**: Yes
- **Response**: File image stream (JPG/PNG).

#### 4. Approval/Verifikasi Transaksi
Persetujuan atau penolakan nominal wakaf yang masuk.
- **Method & URL**: `PUT` `/api/nazhir/transaksi/{id}/approve`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "status_pembayaran": 1
  }
  ```
  *(Value: `1` = Approved, `2` = Rejected)*
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Transaksi berhasil disetujui"
  }
  ```

---

### D. Manajemen Program Wakaf

#### 1. Daftar Program Wakaf Keseluruhan
- **Method & URL**: `GET` `/api/nazhir/program`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id_program": 1,
        "nama_program": "Wakaf Sumur Bor Ponpes",
        "target_dana": 25000000,
        "dana_terkumpul": 12500000,
        "due_date": "2026-12-31",
        "status_program": 1
      }
    ]
  }
  ```

#### 2. Tambah Program Baru
- **Method & URL**: `POST` `/api/nazhir/program`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "nama_program": "Pembangunan Jembatan Madrasah",
    "deskripsi": "<p>Program pembuatan jembatan penghubung...</p>",
    "target_dana": 45000000,
    "due_date": "2027-03-31"
  }
  ```
- **Response Sukses (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Program berhasil dibuat"
  }
  ```

#### 3. Edit Data Program
- **Method & URL**: `PUT` `/api/nazhir/program/{id}`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "nama_program": "Pembangunan Jembatan Madrasah Tahap 1",
    "deskripsi": "<p>Deskripsi diperbarui...</p>",
    "target_dana": 50000000,
    "due_date": "2027-03-31",
    "status_program": 1
  }
  ```
  *(status_program: `1` = Aktif/Buka, `0` = Selesai/Tutup)*
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Program berhasil diperbarui"
  }
  ```

#### 4. Hapus Program
- **Method & URL**: `DELETE` `/api/nazhir/program/{id}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Program berhasil dihapus"
  }
  ```

#### 5. Upload Gambar/Media untuk Deskripsi (Rich Text Editor)
Digunakan oleh editor WYSIWYG untuk mengunggah gambar pendukung program.
- **Method & URL**: `POST` `/api/nazhir/upload-image`
- **Auth Required**: Yes
- **Request Body (Multipart/Form-Data)**:
  - `image` (file binary gambar)
- **Response Sukses (200 OK)**:
  ```json
  {
    "url": "http://127.0.0.1:8000/storage/editor/xyz123.png"
  }
  ```

---

### E. Manajemen Laporan Penyaluran

#### 1. Cek Status Eksistensi Laporan Program
Mengetahui apakah program tertentu sudah pernah dibuatkan laporan penyaluran atau belum.
- **Method & URL**: `GET` `/api/nazhir/laporan/status/{programId}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "exists": true,
    "id_laporan": 1
  }
  ```

#### 2. Get Laporan Berdasarkan ID Program
- **Method & URL**: `GET` `/api/nazhir/laporan/{programId}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id_laporan": 1,
      "id_program": 1,
      "judul_laporan": "Penyaluran Pompa Sumur Bor Ponpes",
      "keterangan": "...",
      "dana_disalurkan": 5000000
    }
  }
  ```

#### 3. Detail Laporan Berdasarkan ID Laporan
- **Method & URL**: `GET` `/api/nazhir/laporan/detail/{id}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id_laporan": 1,
      "id_program": 1,
      "judul_laporan": "Penyaluran Pompa Sumur Bor Ponpes",
      "keterangan": "...",
      "dana_disalurkan": 5000000,
      "created_at": "2026-06-10 15:30:00"
    }
  }
  ```

#### 4. Buat Laporan Penyaluran Baru
Menambahkan berita penyaluran dana wakaf yang terkumpul pada suatu program.
- **Method & URL**: `POST` `/api/nazhir/laporan`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "id_program": 1,
    "judul_laporan": "Penyaluran Pompa Sumur Bor Ponpes",
    "keterangan": "<p>Laporan detail realisasi penyaluran dana...</p>",
    "dana_disalurkan": 5000000
  }
  ```
- **Response Sukses (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Laporan berhasil dibuat"
  }
  ```

#### 5. Perbarui Laporan Penyaluran
- **Method & URL**: `PUT` `/api/nazhir/laporan/{id}`
- **Auth Required**: Yes
- **Request Body (JSON)**:
  ```json
  {
    "id_program": 1,
    "judul_laporan": "Penyaluran Pompa Sumur Bor Ponpes (Revisi)",
    "keterangan": "<p>Revisi keterangan penyaluran...</p>",
    "dana_disalurkan": 5500000
  }
  ```
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Laporan berhasil diperbarui"
  }
  ```

#### 6. Hapus Laporan Penyaluran
- **Method & URL**: `DELETE` `/api/nazhir/laporan/{id}`
- **Auth Required**: Yes
- **Response Sukses (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Laporan berhasil dihapus"
  }
  ```
