<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\T03ProgramWakaf;
use App\Models\T04Transaksi;
use App\Models\T05LaporanPenyaluran;
use App\Models\T02User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class T03ProgramWakafSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create or update Wakif Users (5 akun total - 1 existing + 4 baru)
        $wakifUser = T02User::updateOrCreate(
            ['email' => 'wakif@example.com'],
            [
                'id_role' => 2,
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'no_hp' => '081234567890',
                'jenis_kelamin' => 'L',
                'alamat' => 'Bandung, Jawa Barat',
                'tanggal_lahir' => '1995-05-10',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()->subMonths(6),
                'edited_at' => Carbon::now()->subMonths(6)
            ]
        );

        $wakifUser2 = T02User::updateOrCreate(
            ['email' => 'siti.aminah@example.com'],
            [
                'id_role' => 2,
                'nama' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'no_hp' => '085511223344',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jakarta, DKI Jakarta',
                'tanggal_lahir' => '1988-08-20',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()->subMonths(5),
                'edited_at' => Carbon::now()->subMonths(5)
            ]
        );

        $wakifUser3 = T02User::updateOrCreate(
            ['email' => 'ahmad.fauzi@example.com'],
            [
                'id_role' => 2,
                'nama' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'no_hp' => '087766554433',
                'jenis_kelamin' => 'L',
                'alamat' => 'Surabaya, Jawa Timur',
                'tanggal_lahir' => '1990-03-15',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()->subMonths(4),
                'edited_at' => Carbon::now()->subMonths(4)
            ]
        );

        $wakifUser4 = T02User::updateOrCreate(
            ['email' => 'fatimah.zahra@example.com'],
            [
                'id_role' => 2,
                'nama' => 'Fatimah Zahra',
                'password' => Hash::make('password'),
                'no_hp' => '08111222333',
                'jenis_kelamin' => 'P',
                'alamat' => 'Yogyakarta, DIY',
                'tanggal_lahir' => '1992-11-05',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()->subMonths(3),
                'edited_at' => Carbon::now()->subMonths(3)
            ]
        );

        $wakifUser5 = T02User::updateOrCreate(
            ['email' => 'rahmat.hidayat@example.com'],
            [
                'id_role' => 2,
                'nama' => 'Rahmat Hidayat',
                'password' => Hash::make('password'),
                'no_hp' => '08222333444',
                'jenis_kelamin' => 'L',
                'alamat' => 'Medan, Sumatera Utara',
                'tanggal_lahir' => '1985-07-25',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now()->subMonths(2),
                'edited_at' => Carbon::now()->subMonths(2)
            ]
        );

        // Create or update Nazhir User
        $nazhirUser = T02User::updateOrCreate(
            ['email' => 'nazhir@example.com'],
            [
                'id_role' => 1,
                'nama' => 'Ust. Ahmad',
                'password' => Hash::make('password'),
                'no_hp' => '081298765432',
                'jenis_kelamin' => 'L',
                'alamat' => 'Bandung, Jawa Barat',
                'tanggal_lahir' => '1985-02-15',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now()
            ]
        );

        // Clean up first to avoid constraint/duplicate key issues if running again
        T05LaporanPenyaluran::query()->delete();
        T04Transaksi::query()->delete();
        T03ProgramWakaf::query()->delete();

        // Programs - dana_terkumpul is initial placeholder; transactions will recalculate it
        $programs = [
            [
                'id_program' => 1,
                'nama_program' => 'Pembangunan Sumur Wakaf di Pelosok Desa',
                'deskripsi' => 'Program pengadaan air bersih melalui pembangunan sumur wakaf untuk meringankan krisis air pada musim kemarau di desa terpencil. Pahala mengalir bersama air yang diminum warga.',
                'target_dana' => 50000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(6)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/sumur.jpeg'
            ],
            [
                'id_program' => 2,
                'nama_program' => 'Wakaf Masjid Jami Al-Hidayah',
                'deskripsi' => 'Perluasan pembangunan masjid meliputi renovasi atap, ruang shalat utama, dan tempat wudhu agar bisa diisi kapasitas hingga tiga ratus jamaah dari desa sekitar.',
                'target_dana' => 125000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(12)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/masjid jami.jpg'
            ],
            [
                'id_program' => 3,
                'nama_program' => 'Wakaf Produktif Moda Transportasi Santri',
                'deskripsi' => 'Pengadaan mobil jenazah dan bus operasional untuk santri penghafal Al-Qur\'an guna kegiatan harian dan pengiriman tugas dakwah.',
                'target_dana' => 300000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(3)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/transport.jpg'
            ],
            [
                'id_program' => 4,
                'nama_program' => 'Santunan Yatim dan Pembangunan Panti',
                'deskripsi' => 'Wakaf pembangunan panti asuhan yang dilengkapi asrama, perpustakaan, serta ruang belajar komprehensif bagi lima puluh anak yatim dhuafa.',
                'target_dana' => 75000000,
                'dana_terkumpul' => 0,
                'status_program' => 0, // Telah tercapai/selesai
                'due_date' => Carbon::now()->subDays(2)->toDateString(),
                'created_at' => Carbon::now()->subMonths(8),
                'edited_at' => Carbon::now()->subDays(1),
                'gambar_thumbnail' => '/storage/programs/yatim.png'
            ],
            [
                'id_program' => 5,
                'nama_program' => 'Pembangunan Asrama Santri Tahfidz',
                'deskripsi' => 'Pembangunan fasilitas asrama santri untuk mendukung program tahfidz Al-Qur\'an intensif di tahun mendatang agar proses belajar berjalan kondusif.',
                'target_dana' => 150000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addYear()->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/asrama.webp'
            ],
            [
                'id_program' => 6,
                'nama_program' => 'Wakaf Al-Qur\'an dan Iqra untuk TPA',
                'deskripsi' => 'Program pembagian mushaf Al-Qur\'an dan Iqra baru serta layak untuk anak-anak santri TPA di pedalaman nusantara agar dapat belajar dengan nyaman.',
                'target_dana' => 25000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(4)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alquran.jpg'
            ],
            [
                'id_program' => 7,
                'nama_program' => 'Wakaf Alat Shalat dan Mukena Layak',
                'deskripsi' => 'Pengadaan mukena, sarung, sajadah, dan alat shalat berkualitas untuk masjid-masjid di daerah pelosok yang kekurangan fasilitas ibadah layak.',
                'target_dana' => 15000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(2)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alat shalat.jpg'
            ],
            [
                'id_program' => 8,
                'nama_program' => 'Pembangunan Madrasah Baiturrahman',
                'deskripsi' => 'Wakaf pembangunan gedung madrasah baru untuk memfasilitasi kegiatan belajar mengajar keagamaan santri agar tidak perlu bergantian ruangan kelas lagi.',
                'target_dana' => 200000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(9)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/madrasah.webp'
            ],
            [
                'id_program' => 9,
                'nama_program' => 'Wakaf Ambulans Gratis untuk Umat',
                'deskripsi' => 'Pengadaan mobil pelayanan ambulans gratis untuk mengantarkan pasien sakit maupun jenazah dari kalangan dhuafa di wilayah Bandung dan sekitarnya.',
                'target_dana' => 250000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(8)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/transport.jpg'
            ],
            [
                'id_program' => 10,
                'nama_program' => 'Pembangunan MCK Layak untuk Santri',
                'deskripsi' => 'Pembangunan sarana Mandi, Cuci, dan Kakus (MCK) yang bersih dan representatif untuk menjaga kesehatan serta kebersihan lingkungan pesantren santri yatim.',
                'target_dana' => 35000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(3)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/asrama.webp'
            ],
            [
                'id_program' => 11,
                'nama_program' => 'Wakaf Panel Surya untuk Penerangan Masjid',
                'deskripsi' => 'Pemasangan sistem energi ramah lingkungan berupa panel surya untuk menunjang listrik dan penerangan masjid pelosok agar hemat biaya operasional.',
                'target_dana' => 60000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(5)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/masjid jami.jpg'
            ],
            [
                'id_program' => 12,
                'nama_program' => 'Pengadaan Karpet Masjid Jami',
                'deskripsi' => 'Pengadaan karpet sajadah shalat baru yang lembut, tebal, dan bersih demi kenyamanan para jamaah saat menunaikan ibadah shalat berjamaah.',
                'target_dana' => 20000000,
                'dana_terkumpul' => 0,
                'status_program' => 0,
                'due_date' => Carbon::now()->subDays(10)->toDateString(),
                'created_at' => Carbon::now()->subMonths(1),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alat shalat.jpg'
            ],
            [
                'id_program' => 13,
                'nama_program' => 'Wakaf Al-Qur\'an Braille untuk Difabel Netra',
                'deskripsi' => 'Pemberian mushaf Al-Qur\'an khusus Braille untuk memudahkan saudara-saudara kita penyandang disabilitas netra dalam belajar dan menghafal kalamullah.',
                'target_dana' => 40000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(6)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alquran.jpg'
            ],
            [
                'id_program' => 14,
                'nama_program' => 'Renovasi Jembatan Penghubung Desa',
                'deskripsi' => 'Wakaf infrastruktur pembangunan jembatan kayu yang sudah rapuh menjadi jembatan beton kokoh guna mempermudah akses anak-anak menuju sekolah dan masjid.',
                'target_dana' => 90000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(7)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/madrasah.webp'
            ],
            [
                'id_program' => 15,
                'nama_program' => 'Wakaf Sumur Bor Pertanian Produktif',
                'deskripsi' => 'Pembangunan sumur bor air tanah untuk mengairi lahan pertanian wakaf produktif yang dikelola masyarakat miskin guna mewujudkan ketahanan pangan lokal.',
                'target_dana' => 70000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(4)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/sumur.jpeg'
            ],
            [
                'id_program' => 16,
                'nama_program' => 'Pengadaan Sound System Masjid Baitussalam',
                'deskripsi' => 'Wakaf pengadaan pengeras suara dan sound system masjid yang jernih agar kumandang adzan dan penyampaian khutbah keagamaan terdengar jelas oleh warga desa.',
                'target_dana' => 15000000,
                'dana_terkumpul' => 0,
                'status_program' => 0,
                'due_date' => Carbon::now()->subDays(15)->toDateString(),
                'created_at' => Carbon::now()->subMonths(2),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alat shalat.jpg'
            ],
            [
                'id_program' => 17,
                'nama_program' => 'Pembangunan Laboratorium Komputer Santri',
                'deskripsi' => 'Pembangunan ruang kelas baru khusus IT beserta pengadaan laptop dan koneksi internet bagi santri yatim dhuafa agar melek teknologi di era digital.',
                'target_dana' => 180000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(10)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/madrasah.webp'
            ],
            [
                'id_program' => 18,
                'nama_program' => 'Penyediaan Kitab Kuning untuk Pesantren',
                'deskripsi' => 'Pengadaan kitab-kitab rujukan (kitab kuning) klasik terlengkap bagi para santri tingkat menengah dan tinggi dalam memperdalam ilmu fikih, tafsir, dan hadits.',
                'target_dana' => 25000000,
                'dana_terkumpul' => 0,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(3)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => '/storage/programs/alquran.jpg'
            ],
        ];

        foreach ($programs as $prog) {
            T03ProgramWakaf::create($prog);
        }

        // Transactions - these will trigger recalculateProgramFunds via model events
        // Target dana_terkumpul per program (approved transactions):
        // Program 1: 15,000,000 | Program 2: 45,000,000 | Program 3: 200,000,000
        // Program 4: 75,000,000 | Program 5: 0 | Program 6: 12,000,000
        // Program 7: 45,000,000 | Program 8: 140,000,000 | Program 9: 85,000,000
        // Program 10: 12,000,000 | Program 11: 30,000,000 | Program 12: 20,000,000
        // Program 13: 15,000,000 | Program 14: 45,000,000 | Program 15: 50,000,000
        // Program 16: 15,000,000 | Program 17: 60,000,000 | Program 18: 10,000,000

        $idx = 1;
        $transactions = [
            // ===== PROGRAM 1 (target: 15,000,000) =====
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga dimudahkan segala urusan dan menjadi amal jariyah.',
                'email' => 'wakif@example.com',
                'id_program' => 1,
                'nominal' => 5000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(0)->addDays(10),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(0)->addDays(10),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            [
                'id_user' => $wakifUser2->id_user,
                'nama' => 'Siti Aminah',
                'pesan_doa' => 'Bismillah, semoga berkah.',
                'email' => 'siti.aminah@example.com',
                'id_program' => 1,
                'nominal' => 10000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(1)->addDays(15),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(1)->addDays(15),
                'no_hp' => '085511223344',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 2 (target: 45,000,000) =====
            [
                'id_user' => $wakifUser3->id_user,
                'nama' => 'Ahmad Fauzi',
                'pesan_doa' => 'Untuk almarhum orang tua saya.',
                'email' => 'ahmad.fauzi@example.com',
                'id_program' => 2,
                'nominal' => 25000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(2)->addDays(5),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(2)->addDays(5),
                'no_hp' => '087766554433',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga pembangunan cepat selesai.',
                'email' => 'donatur@example.com',
                'id_program' => 2,
                'nominal' => 20000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(3)->addDays(22),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(3)->addDays(22),
                'no_hp' => '085511223345',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 3 (target: 200,000,000) =====
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga berkah untuk para santri.',
                'email' => 'wakif@example.com',
                'id_program' => 3,
                'nominal' => 100000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(12),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(12),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            [
                'id_user' => $wakifUser4->id_user,
                'nama' => 'Fatimah Zahra',
                'pesan_doa' => 'Bismillah operasional lancar.',
                'email' => 'fatimah.zahra@example.com',
                'id_program' => 3,
                'nominal' => 100000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(28),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(28),
                'no_hp' => '08111222333',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 4 (target: 75,000,000) =====
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Santunan anak yatim piatu.',
                'email' => 'donatur2@example.com',
                'id_program' => 4,
                'nominal' => 75000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->startOfYear()->addMonths(5)->addDays(18),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(5)->addDays(18),
                'no_hp' => '08111222334',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 5 - no approved transactions (dana_terkumpul = 0) =====
            // Only pending transactions:
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Wakaf pembangunan asrama santri.',
                'email' => 'wakif@example.com',
                'id_program' => 5,
                'nominal' => 5000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 0, // Pending
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 6 (target: 12,000,000) =====
            [
                'id_user' => $wakifUser5->id_user,
                'nama' => 'Rahmat Hidayat',
                'pesan_doa' => 'Semoga anak-anak TPA bisa belajar dengan baik.',
                'email' => 'rahmat.hidayat@example.com',
                'id_program' => 6,
                'nominal' => 7000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(2)->addDays(5),
                'edited_at' => Carbon::now()->subMonths(2)->addDays(5),
                'no_hp' => '08222333444',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Mudah-mudahan bermanfaat.',
                'email' => 'donatur3@example.com',
                'id_program' => 6,
                'nominal' => 5000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(1)->addDays(10),
                'edited_at' => Carbon::now()->subMonths(1)->addDays(10),
                'no_hp' => '08333444555',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 7 (target: 45,000,000) =====
            [
                'id_user' => $wakifUser2->id_user,
                'nama' => 'Siti Aminah',
                'pesan_doa' => 'Semoga jamaah masjid semakin khusyuk beribadah.',
                'email' => 'siti.aminah@example.com',
                'id_program' => 7,
                'nominal' => 20000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(3)->addDays(8),
                'edited_at' => Carbon::now()->subMonths(3)->addDays(8),
                'no_hp' => '085511223344',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Lillahi ta\'ala.',
                'email' => 'donatur4@example.com',
                'id_program' => 7,
                'nominal' => 25000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(2)->addDays(20),
                'edited_at' => Carbon::now()->subMonths(2)->addDays(20),
                'no_hp' => '08444555666',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 8 (target: 140,000,000) =====
            [
                'id_user' => $wakifUser3->id_user,
                'nama' => 'Ahmad Fauzi',
                'pesan_doa' => 'Semoga madrasah segera berdiri dan bermanfaat.',
                'email' => 'ahmad.fauzi@example.com',
                'id_program' => 8,
                'nominal' => 80000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(4)->addDays(15),
                'edited_at' => Carbon::now()->subMonths(4)->addDays(15),
                'no_hp' => '087766554433',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga anak-anak santri mendapat ilmu yang bermanfaat.',
                'email' => 'donatur5@example.com',
                'id_program' => 8,
                'nominal' => 60000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(3)->addDays(25),
                'edited_at' => Carbon::now()->subMonths(3)->addDays(25),
                'no_hp' => '08555666777',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 9 (target: 85,000,000) =====
            [
                'id_user' => $wakifUser4->id_user,
                'nama' => 'Fatimah Zahra',
                'pesan_doa' => 'Semoga ambulans bisa membantu masyarakat yang membutuhkan.',
                'email' => 'fatimah.zahra@example.com',
                'id_program' => 9,
                'nominal' => 50000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(5)->addDays(12),
                'edited_at' => Carbon::now()->subMonths(5)->addDays(12),
                'no_hp' => '08111222333',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Bismillah.',
                'email' => 'donatur6@example.com',
                'id_program' => 9,
                'nominal' => 35000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(4)->addDays(5),
                'edited_at' => Carbon::now()->subMonths(4)->addDays(5),
                'no_hp' => '08666777888',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 10 (target: 12,000,000) =====
            [
                'id_user' => $wakifUser5->id_user,
                'nama' => 'Rahmat Hidayat',
                'pesan_doa' => 'Semoga santri sehat dan nyaman.',
                'email' => 'rahmat.hidayat@example.com',
                'id_program' => 10,
                'nominal' => 12000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(2)->addDays(3),
                'edited_at' => Carbon::now()->subMonths(2)->addDays(3),
                'no_hp' => '08222333444',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 11 (target: 30,000,000) =====
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga masjid hemat energi dan semakin berkah.',
                'email' => 'wakif@example.com',
                'id_program' => 11,
                'nominal' => 30000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(1)->addDays(7),
                'edited_at' => Carbon::now()->subMonths(1)->addDays(7),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 12 (target: 20,000,000) =====
            [
                'id_user' => $wakifUser2->id_user,
                'nama' => 'Siti Aminah',
                'pesan_doa' => 'Semoga jamaah masjid nyaman beribadah.',
                'email' => 'siti.aminah@example.com',
                'id_program' => 12,
                'nominal' => 20000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(2)->addDays(18),
                'edited_at' => Carbon::now()->subMonths(2)->addDays(18),
                'no_hp' => '085511223344',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 13 (target: 15,000,000) =====
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga saudara kita yang difabel bisa membaca Al-Qur\'an.',
                'email' => 'donatur7@example.com',
                'id_program' => 13,
                'nominal' => 15000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(3)->addDays(10),
                'edited_at' => Carbon::now()->subMonths(3)->addDays(10),
                'no_hp' => '08777888999',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 14 (target: 45,000,000) =====
            [
                'id_user' => $wakifUser3->id_user,
                'nama' => 'Ahmad Fauzi',
                'pesan_doa' => 'Semoga jembatan ini menjadi jalan kebaikan.',
                'email' => 'ahmad.fauzi@example.com',
                'id_program' => 14,
                'nominal' => 25000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(4)->addDays(22),
                'edited_at' => Carbon::now()->subMonths(4)->addDays(22),
                'no_hp' => '087766554433',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Barakallah.',
                'email' => 'donatur8@example.com',
                'id_program' => 14,
                'nominal' => 20000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(3)->addDays(15),
                'edited_at' => Carbon::now()->subMonths(3)->addDays(15),
                'no_hp' => '08888999000',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 15 (target: 50,000,000) =====
            [
                'id_user' => $wakifUser4->id_user,
                'nama' => 'Fatimah Zahra',
                'pesan_doa' => 'Semoga pertanian wakaf ini menghasilkan bagi umat.',
                'email' => 'fatimah.zahra@example.com',
                'id_program' => 15,
                'nominal' => 30000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(2)->addDays(8),
                'edited_at' => Carbon::now()->subMonths(2)->addDays(8),
                'no_hp' => '08111222333',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga bermanfaat bagi masyarakat.',
                'email' => 'donatur9@example.com',
                'id_program' => 15,
                'nominal' => 20000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(1)->addDays(12),
                'edited_at' => Carbon::now()->subMonths(1)->addDays(12),
                'no_hp' => '08999000111',
                'hide_nama' => 1
            ],
            // ===== PROGRAM 16 (target: 15,000,000) =====
            [
                'id_user' => $wakifUser5->id_user,
                'nama' => 'Rahmat Hidayat',
                'pesan_doa' => 'Semoga adzan terdengar jauh dan masjid ramai.',
                'email' => 'rahmat.hidayat@example.com',
                'id_program' => 16,
                'nominal' => 15000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(3)->addDays(5),
                'edited_at' => Carbon::now()->subMonths(3)->addDays(5),
                'no_hp' => '08222333444',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 17 (target: 60,000,000) =====
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga santri bisa melek teknologi.',
                'email' => 'wakif@example.com',
                'id_program' => 17,
                'nominal' => 60000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(5)->addDays(3),
                'edited_at' => Carbon::now()->subMonths(5)->addDays(3),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            // ===== PROGRAM 18 (target: 10,000,000) =====
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga kitab ini menjadi ilmu yang bermanfaat.',
                'email' => 'donatur10@example.com',
                'id_program' => 18,
                'nominal' => 10000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1,
                'kode_referensi' => 'INV-G-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subMonths(1)->addDays(20),
                'edited_at' => Carbon::now()->subMonths(1)->addDays(20),
                'no_hp' => '08000111222',
                'hide_nama' => 1
            ],
            // ===== PENDING TRANSACTIONS (wakif akun utama) =====
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Wakaf pembangunan sumur desa.',
                'email' => 'wakif@example.com',
                'id_program' => 1,
                'nominal' => 2500000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 0, // Pending
                'kode_referensi' => 'INV-U-' . time() . '-' . ($idx++),
                'created_at' => Carbon::now()->subDays(1),
                'edited_at' => Carbon::now()->subDays(1),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
        ];

        foreach ($transactions as $tx) {
            T04Transaksi::create($tx);
        }

        // Seed some Laporan Penyaluran (News Reports)
        $reports = [
            [
                'id_program' => 1,
                'judul_laporan' => 'Pengeboran Air Bersih Dimulai',
                'keterangan' => 'Alhamdulillah, hari ini tim teknis mulai melakukan survei geolistrik dan pengeboran titik sumur wakaf pertama di pelosok desa. Terima kasih para donatur.<br/><br/><img src="https://images.unsplash.com/photo-1581094288338-2314dddb7ecc?auto=format&fit=crop&q=80&w=600" alt="Pengeboran Air Bersih" /><br/><img src="https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&q=80&w=600" alt="Survei Lokasi" />',
                'dana_disalurkan' => 5000000,
                'penerima_manfaat' => 150,
                'created_at' => Carbon::now()->subDays(4),
                'edited_at' => Carbon::now()->subDays(4),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1581094288338-2314dddb7ecc?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 2,
                'judul_laporan' => 'Pembelian Material Semen dan Pasir',
                'keterangan' => 'Penyaluran tahap pertama digunakan untuk membeli material pondasi masjid seperti semen, pasir, dan besi beton. Pembangunan fisik segera dimulai.<br/><br/><img src="https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&q=80&w=600" alt="Pembelian Material" />',
                'dana_disalurkan' => 15000000,
                'penerima_manfaat' => 300,
                'created_at' => Carbon::now()->subDays(6),
                'edited_at' => Carbon::now()->subDays(6),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 3,
                'judul_laporan' => 'Pembelian Unit Armada Pertama',
                'keterangan' => 'Alhamdulillah, berkat dukungan para donatur, unit mobil operasional pertama telah dibeli dan siap digunakan untuk mobilitas dakwah santri.<br/><br/><img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=600" alt="Mobil Santri" />',
                'dana_disalurkan' => 80000000,
                'penerima_manfaat' => 250,
                'created_at' => Carbon::now()->subDays(3),
                'edited_at' => Carbon::now()->subDays(3),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 4,
                'judul_laporan' => 'Penyerahan Santunan Yatim Piatu',
                'keterangan' => 'Penyaluran dana wakaf panti asuhan dan santunan telah diberikan kepada lima puluh anak yatim piatu di yayasan Baiturrahman secara berkala.<br/><br/><img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=600" alt="Santunan Yatim" />',
                'dana_disalurkan' => 25000000,
                'penerima_manfaat' => 50,
                'created_at' => Carbon::now()->subDays(5),
                'edited_at' => Carbon::now()->subDays(5),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 5,
                'judul_laporan' => 'Pemasangan Tiang Pancang Asrama',
                'keterangan' => 'Fase peletakan batu pertama dan pemasangan tiang pancang asrama baru santri tahfidz telah selesai dilakukan dengan lancar.<br/><br/><img src="https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&q=80&w=600" alt="Tiang Asrama" />',
                'dana_disalurkan' => 35000000,
                'penerima_manfaat' => 80,
                'created_at' => Carbon::now()->subDays(2),
                'edited_at' => Carbon::now()->subDays(2),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 6,
                'judul_laporan' => 'Distribusi 500 Mushaf Al-Qur\'an',
                'keterangan' => 'Tim relawan telah mendistribusikan ratusan mushaf Al-Qur\'an baru dan Iqra ke TPA terpencil di pelosok nusantara.<br/><br/><img src="https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&q=80&w=600" alt="Distribusi Quran" />',
                'dana_disalurkan' => 8000000,
                'penerima_manfaat' => 500,
                'created_at' => Carbon::now()->subDays(1),
                'edited_at' => Carbon::now()->subDays(1),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 7,
                'judul_laporan' => 'Penyaluran Mukena dan Sajadah Baru',
                'keterangan' => 'Alhamdulillah, puluhan paket mukena dan sajadah baru telah diterima dengan bahagia oleh jamaah masjid Baitul Mukmin di kaki gunung.<br/><br/><img src="https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&q=80&w=600" alt="Penyaluran Mukena" />',
                'dana_disalurkan' => 5000000,
                'penerima_manfaat' => 120,
                'created_at' => Carbon::now()->subDays(4),
                'edited_at' => Carbon::now()->subDays(4),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 8,
                'judul_laporan' => 'Pemasangan Keramik Kelas Madrasah',
                'keterangan' => 'Progress pembangunan fisik gedung madrasah terus berlanjut. Saat ini tim sedang mengerjakan pemasangan keramik lantai dan pengecatan tembok kelas.<br/><br/><img src="https://images.unsplash.com/photo-1541944743827-e04aa6427c33?auto=format&fit=crop&q=80&w=600" alt="Pemasangan Keramik" />',
                'dana_disalurkan' => 45000000,
                'penerima_manfaat' => 180,
                'created_at' => Carbon::now()->subDays(7),
                'edited_at' => Carbon::now()->subDays(7),
                'gambar_laporan' => 'https://images.unsplash.com/photo-1541944743827-e04aa6427c33?auto=format&fit=crop&q=80&w=600'
            ],
        ];

        foreach ($reports as $rep) {
            T05LaporanPenyaluran::create($rep);
        }

        // Clean up first
        \App\Models\T06PencairanDana::query()->delete();

        // Seed some Pencairan Dana
        $pencairans = [
            [
                'id_program' => 1,
                'id_user' => $nazhirUser->id_user,
                'jumlah_dana' => 5000000.00,
                'keterangan' => 'Penyaluran tahap awal untuk pengeboran sumur dan pipa air.',
                'status_pencairan' => 1, // Approved
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'id_program' => 3,
                'id_user' => $nazhirUser->id_user,
                'jumlah_dana' => 80000000.00,
                'keterangan' => 'Pembelian unit armada mobil operasional santri.',
                'status_pencairan' => 1, // Approved
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'id_program' => 2,
                'id_user' => $nazhirUser->id_user,
                'jumlah_dana' => 15000000.00,
                'keterangan' => 'Pembelian material pondasi, besi cor, semen, dan pasir.',
                'status_pencairan' => 0, // Pending
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'id_program' => 8,
                'id_user' => $nazhirUser->id_user,
                'jumlah_dana' => 45000000.00,
                'keterangan' => 'Pemasangan keramik, pengecatan, dan finishing interior kelas madrasah.',
                'status_pencairan' => 0, // Pending
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($pencairans as $pc) {
            \App\Models\T06PencairanDana::create($pc);
        }

        if (config('database.default') === 'pgsql') {
            $tables = [
                't01_roles' => 'id_role',
                't02_users' => 'id_user',
                't03_program_wakaf' => 'id_program',
                't04_transaksi' => 'id_transaksi',
                't05_laporan_penyaluran' => 'id_laporan',
                't06_pencairan_dana' => 'id_pencairan',
            ];
            foreach ($tables as $table => $pk) {
                \Illuminate\Support\Facades\DB::statement("SELECT setval(pg_get_serial_sequence('$table', '$pk'), coalesce(max($pk), 1)) FROM $table");
            }
        }
    }
}
