<?php

namespace App\Repositories\Wakif;

use App\Models\T03ProgramWakaf;
use App\Models\T04Transaksi;
use App\Models\T05LaporanPenyaluran;
use App\RepositoryInterfaces\Wakif\WakifDashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WakifDashboardRepository implements WakifDashboardRepositoryInterface
{
    public function getCounters($bulan = null, $tahun = null)
    {
        T03ProgramWakaf::where('status_program', '!=', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', \Carbon\Carbon::now()->toDateString())
            ->update(['status_program' => 0]);

        // Program Count
        $programQuery = T03ProgramWakaf::query();
        if ($tahun) {
            $programQuery->whereYear('created_at', '<=', $tahun);
            if ($bulan) {
                $programQuery->where(function($q) use ($bulan, $tahun) {
                    $q->whereYear('created_at', '<', $tahun)
                      ->orWhere(function($sub) use ($bulan, $tahun) {
                          $sub->whereYear('created_at', $tahun)
                              ->whereMonth('created_at', '<=', $bulan);
                      });
                });
            }
        }
        $programCount = $programQuery->count();

        // Wakaf Terkumpul
        $wakafQuery = T04Transaksi::where('status_pembayaran', 1);
        if ($tahun) {
            $wakafQuery->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $wakafQuery->whereMonth('created_at', $bulan);
        }
        $totalWakaf = $wakafQuery->sum('nominal') ?? 0;

        // Donatur
        $donaturQuery = T04Transaksi::where('status_pembayaran', 1);
        if ($tahun) {
            $donaturQuery->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $donaturQuery->whereMonth('created_at', $bulan);
        }
        $totalDonaturKeseluruhan = $donaturQuery->count();

        // Penerima Manfaat
        $laporanQuery = T05LaporanPenyaluran::query();
        if ($tahun) {
            $laporanQuery->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $laporanQuery->whereMonth('created_at', $bulan);
        }
        $penerimaManfaat = $laporanQuery->sum('penerima_manfaat') ?? 0;
        
        return [
            'program' => $programCount,
            'wakaf_terkumpul' => $totalWakaf,
            'donatur' => $totalDonaturKeseluruhan,
            'penerima_manfaat' => $penerimaManfaat
        ];
    }

    public function getProgramWakafList()
    {
        T03ProgramWakaf::where('status_program', '!=', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', \Carbon\Carbon::now()->toDateString())
            ->update(['status_program' => 0]);

        return T03ProgramWakaf::select('id_program', 'nama_program', 'deskripsi', 'target_dana', 'dana_terkumpul', 'due_date', 'status_program', 'gambar_thumbnail')
            ->where('status_program', 1)
            ->orderBy('id_program', 'desc')
            ->get();
    }

    public function getProgramById($id)
    {
        return T03ProgramWakaf::find($id);
    }

    public function getProgramDeskripsi($id)
    {
        return T03ProgramWakaf::select('nama_program', 'deskripsi')->where('id_program', $id)->first();
    }

    public function getCounterDonatur($idProgram = null)
    {
        $query = T04Transaksi::query()->where('status_pembayaran', 1); // only count approved/successful donations as donors
        if ($idProgram) {
            $query->where('id_program', $idProgram);
        }
        return $query->count();
    }

    public function getProgramCountdown($id)
    {
        $program = T03ProgramWakaf::find($id);
        if (!$program || !$program->due_date) {
            return [
                'days_remaining' => 0,
                'formatted' => 'Selesai'
            ];
        }

        $dueDate = \Carbon\Carbon::parse($program->due_date)->startOfDay();
        $now = \Carbon\Carbon::now()->startOfDay();

        if ($now->greaterThan($dueDate)) {
            return [
                'days_remaining' => 0,
                'formatted' => 'Selesai'
            ];
        }

        $diff = $now->diffInDays($dueDate);
        if ($diff === 0) {
            return [
                'days_remaining' => 0,
                'formatted' => 'Hari Ini'
            ];
        }

        return [
            'days_remaining' => $diff,
            'formatted' => $diff . ' Hari Lagi'
        ];
    }

    public function getDonaturByProgram($idProgram, array $filters)
    {
        $query = T04Transaksi::where('id_program', $idProgram)
            ->where('status_pembayaran', 1)
            ->orderBy('created_at', 'desc');

        if (!empty($filters['start']) && !empty($filters['end'])) {
            $query->whereDate('created_at', '>=', $filters['start'])
                  ->whereDate('created_at', '<=', $filters['end']);
        }

        $limit = $filters['limit'] ?? 10;
        $page  = $filters['page'] ?? 1;
        return $query->select('id_transaksi', 'id_user', 'nama', 'hide_nama', 'nominal', 'pesan_doa', 'created_at')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getBeritaLaporan($idProgram = null)
    {
        $query = T05LaporanPenyaluran::orderBy('created_at', 'desc')
            ->select('id_laporan', 'id_program', 'judul_laporan', 'keterangan', 'dana_disalurkan', 'penerima_manfaat', 'created_at');

        if ($idProgram) {
            $query->where('id_program', $idProgram);
        }

        return $query->get();
    }

    public function getPenyebaranProgram($bulan = null, $tahun = null)
    {
        $totalQuery = T04Transaksi::where('status_pembayaran', 1);
        if ($tahun) {
            $totalQuery->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $totalQuery->whereMonth('created_at', $bulan);
        }
        $totalTransaksi = $totalQuery->count();
        if ($totalTransaksi == 0) return [];

        $txQuery = T04Transaksi::select('id_program', DB::raw('count(*) as count'))
            ->where('status_pembayaran', 1);
        if ($tahun) {
            $txQuery->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $txQuery->whereMonth('created_at', $bulan);
        }
        $programs = $txQuery->groupBy('id_program')->get();

        $result = [];
        foreach ($programs as $prog) {
            $nama = T03ProgramWakaf::where('id_program', $prog->id_program)->value('nama_program');
            if ($nama) {
                $persenan = ($prog->count / $totalTransaksi) * 100;
                $result[] = [
                    'nama_program' => $nama,
                    'persen' => round($persenan, 2)
                ];
            }
        }
        return $result;
    }

    public function getTrendWakafPerTahun($tahun, $bulan = null)
    {
        $driver = DB::connection()->getDriverName();
        
        if ($bulan) {
            $dayQuery = 'EXTRACT(DAY FROM created_at)';
            
            if ($driver === 'sqlite') {
                $dayQuery = "cast(strftime('%d', created_at) as integer)";
            } elseif ($driver === 'mysql') {
                $dayQuery = "DAY(created_at)";
            }

            return T04Transaksi::select(
                DB::raw("$dayQuery as tanggal"),
                DB::raw('SUM(nominal) as wakaf_terkumpul')
            )
                ->where('status_pembayaran', 1)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->groupBy(DB::raw($dayQuery))
                ->orderBy('tanggal')
                ->get();
        } else {
            $monthQuery = 'EXTRACT(MONTH FROM created_at)';
            
            if ($driver === 'sqlite') {
                $monthQuery = "cast(strftime('%m', created_at) as integer)";
            } elseif ($driver === 'mysql') {
                $monthQuery = "MONTH(created_at)";
            }

            return T04Transaksi::select(
                DB::raw("$monthQuery as bulan"),
                DB::raw('SUM(nominal) as wakaf_terkumpul')
            )
                ->where('status_pembayaran', 1)
                ->whereYear('created_at', $tahun)
                ->groupBy(DB::raw($monthQuery))
                ->orderBy('bulan')
                ->get();
        }
    }
}
