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
        // 1. Create or update Wakif User to ensure credentials are correct
        $wakifUser = T02User::updateOrCreate(
            ['email' => 'wakif@example.com'],
            [
                'id_role' => 2, // Wakif
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'no_hp' => '081234567890',
                'jenis_kelamin' => 'L',
                'alamat' => 'Bandung, Jawa Barat',
                'tanggal_lahir' => '1995-05-10',
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now()
            ]
        );

        // Create or update Nazhir User to ensure credentials are correct
        $nazhirUser = T02User::updateOrCreate(
            ['email' => 'nazhir@example.com'],
            [
                'id_role' => 1, // Nazhir
                'nama' => 'Ust. Ahmad',
                'password' => Hash::make('password'),
                'no_hp' => '081298765432',
                'jenis_kelamin' => 'L',
                'alamat' => 'Bandung, Jawa Barat',
                'tanggal_lahir' => '1985-02-15',
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now()
            ]
        );

        // Clean up first to avoid constraint/duplicate key issues if running again
        T05LaporanPenyaluran::query()->delete();
        T04Transaksi::query()->delete();
        T03ProgramWakaf::query()->delete();

        $programs = [
            [
                'id_program' => 1,
                'nama_program' => 'Pembangunan Sumur Wakaf di Pelosok Desa',
                'deskripsi' => 'Program pengadaan air bersih melalui pembangunan sumur wakaf untuk meringankan krisis air pada musim kemarau di desa terpencil. Pahala mengalir bersama air yang diminum warga.',
                'target_dana' => 50000000,
                'dana_terkumpul' => 15000000,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(6)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1541944743827-e04aa6427c33?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 2,
                'nama_program' => 'Wakaf Masjid Jami Al-Hidayah',
                'deskripsi' => 'Perluasan pembangunan masjid meliputi renovasi atap, ruang shalat utama, dan tempat wudhu agar bisa diisi kapasitas hingga tiga ratus jamaah dari desa sekitar.',
                'target_dana' => 125000000,
                'dana_terkumpul' => 45000000,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(12)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1597935258735-e254c1839512?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 3,
                'nama_program' => 'Wakaf Produktif Moda Transportasi Santri',
                'deskripsi' => 'Pengadaan mobil jenazah dan bus operasional untuk santri penghafal Al-Qur\'an guna kegiatan harian dan pengiriman tugas dakwah.',
                'target_dana' => 300000000,
                'dana_terkumpul' => 200000000,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(3)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 4,
                'nama_program' => 'Santunan Yatim dan Pembangunan Panti',
                'deskripsi' => 'Wakaf pembangunan panti asuhan yang dilengkapi asrama, perpustakaan, serta ruang belajar komprehensif bagi lima puluh anak yatim dhuafa.',
                'target_dana' => 75000000,
                'dana_terkumpul' => 75000000,
                'status_program' => 0, // Telah tercapai/selesai
                'due_date' => Carbon::now()->subDays(2)->toDateString(),
                'created_at' => Carbon::now()->subMonths(8),
                'edited_at' => Carbon::now()->subDays(1),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=600'
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
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 6,
                'nama_program' => 'Wakaf Al-Qur\'an dan Iqra untuk TPA',
                'deskripsi' => 'Program pembagian mushaf Al-Qur\'an dan Iqra baru serta layak untuk anak-anak santri TPA di pedalaman nusantara agar dapat belajar dengan nyaman.',
                'target_dana' => 25000000,
                'dana_terkumpul' => 12000000,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(4)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 7,
                'nama_program' => 'Wakaf Alat Shalat dan Mukena Layak',
                'deskripsi' => 'Pengadaan mukena, sarung, sajadah, dan alat shalat berkualitas untuk masjid-masjid di daerah pelosok yang kekurangan fasilitas ibadah layak.',
                'target_dana' => 15000000,
                'dana_terkumpul' => 45000000, // overfunded/completed or normal
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(2)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&q=80&w=600'
            ],
            [
                'id_program' => 8,
                'nama_program' => 'Pembangunan Madrasah Baiturrahman',
                'deskripsi' => 'Wakaf pembangunan gedung madrasah baru untuk memfasilitasi kegiatan belajar mengajar keagamaan santri agar tidak perlu bergantian ruangan kelas lagi.',
                'target_dana' => 200000000,
                'dana_terkumpul' => 140000000,
                'status_program' => 1,
                'due_date' => Carbon::now()->addMonths(9)->toDateString(),
                'created_at' => Carbon::now(),
                'edited_at' => Carbon::now(),
                'gambar_thumbnail' => 'https://images.unsplash.com/photo-1541944743827-e04aa6427c33?auto=format&fit=crop&q=80&w=600'
            ],
        ];

        foreach ($programs as $prog) {
            T03ProgramWakaf::create($prog);
        }

        $transactions = [
            // Program 1
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga dimudahkan segala urusan dan menjadi amal jariyah.',
                'id_program' => 1,
                'nominal' => 5000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-U-' . time() . '-1',
                'created_at' => Carbon::now()->startOfYear()->addMonths(0)->addDays(10),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(0)->addDays(10),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Bismillah, semoga berkah.',
                'id_program' => 1,
                'nominal' => 10000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-G-' . time() . '-2',
                'created_at' => Carbon::now()->startOfYear()->addMonths(1)->addDays(15),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(1)->addDays(15),
                'no_hp' => '08987654321',
                'hide_nama' => 1
            ],
            // Program 2
            [
                'id_user' => null,
                'nama' => 'Siti Aminah',
                'pesan_doa' => 'Untuk almarhum orang tua saya.',
                'id_program' => 2,
                'nominal' => 25000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-G-' . time() . '-3',
                'created_at' => Carbon::now()->startOfYear()->addMonths(2)->addDays(5),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(2)->addDays(5),
                'no_hp' => '085511223344',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Semoga pembangunan cepat selesai.',
                'id_program' => 2,
                'nominal' => 20000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-G-' . time() . '-4',
                'created_at' => Carbon::now()->startOfYear()->addMonths(3)->addDays(22),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(3)->addDays(22),
                'no_hp' => '085511223345',
                'hide_nama' => 1
            ],
            // Program 3
            [
                'id_user' => $wakifUser->id_user,
                'nama' => 'Budi Santoso',
                'pesan_doa' => 'Semoga berkah untuk para santri.',
                'id_program' => 3,
                'nominal' => 100000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-U-' . time() . '-5',
                'created_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(12),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(12),
                'no_hp' => '081234567890',
                'hide_nama' => 0
            ],
            [
                'id_user' => null,
                'nama' => 'Donatur Dermawan',
                'pesan_doa' => 'Bismillah operasional lancar.',
                'id_program' => 3,
                'nominal' => 100000000,
                'bukti_pembayaran' => null,
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-G-' . time() . '-6',
                'created_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(28),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(4)->addDays(28),
                'no_hp' => '087766554433',
                'hide_nama' => 0
            ],
            // Program 4 (Completed)
            [
                'id_user' => null,
                'nama' => 'Hamba Allah',
                'pesan_doa' => 'Santunan anak yatim piatu.',
                'id_program' => 4,
                'nominal' => 75000000,
                'bukti_pembayaran' => 'https://images.unsplash.com/photo-1598501170281-a6cc05307bc1?auto=format&fit=crop&q=80',
                'status_pembayaran' => 1, // Approved
                'kode_referensi' => 'INV-G-' . time() . '-7',
                'created_at' => Carbon::now()->startOfYear()->addMonths(5)->addDays(18),
                'edited_at' => Carbon::now()->startOfYear()->addMonths(5)->addDays(18),
                'no_hp' => '08111222333',
                'hide_nama' => 1
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
    }
}
