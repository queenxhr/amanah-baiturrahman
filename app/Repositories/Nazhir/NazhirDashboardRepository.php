<?php

namespace App\Repositories\Nazhir;

use App\Models\T03ProgramWakaf;
use App\Models\T04Transaksi;
use App\Models\T05LaporanPenyaluran;
use App\RepositoryInterfaces\Nazhir\NazhirDashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class NazhirDashboardRepository implements NazhirDashboardRepositoryInterface
{
    public function getCounters(array $filters)
    {
        T04Transaksi::sweepExpiredTransactions();

        $programQuery = T03ProgramWakaf::query();
        $transaksiQuery = T04Transaksi::query();
        $laporanQuery = T05LaporanPenyaluran::query();

        if (!empty($filters['start']) && !empty($filters['end'])) {
            $programQuery->whereBetween('created_at', [$filters['start'], $filters['end']]);
            $transaksiQuery->whereBetween('created_at', [$filters['start'], $filters['end']]);
            $laporanQuery->whereBetween('created_at', [$filters['start'], $filters['end']]);
        }

        $tahun = $filters['tahun'] ?? null;
        $bulan = $filters['bulan'] ?? null;

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

            $transaksiQuery->whereYear('created_at', $tahun);
            if ($bulan) {
                $transaksiQuery->whereMonth('created_at', $bulan);
            }

            $laporanQuery->whereYear('created_at', $tahun);
            if ($bulan) {
                $laporanQuery->whereMonth('created_at', $bulan);
            }
        }

        if (!empty($filters['program'])) {
            $programQuery->where('id_program', $filters['program']);
            $transaksiQuery->where('id_program', $filters['program']);
            $laporanQuery->where('id_program', $filters['program']);
        }

        $programCount = $programQuery->count();
        $totalWakaf = $transaksiQuery->clone()->where('status_pembayaran', 1)->sum('nominal');
        $totalDonatur = $transaksiQuery->clone()->where('status_pembayaran', 1)->count();
        $penerimaManfaat = $laporanQuery->sum('penerima_manfaat') ?? 0;
        
        return [
            'program' => $programCount,
            'wakaf_terkumpul' => $totalWakaf,
            'donatur' => $totalDonatur,
            'penerima_manfaat' => $penerimaManfaat
        ];
    }

    public function getPenyebaranProgram()
    {
        T04Transaksi::sweepExpiredTransactions();

        $totalTransaksi = T04Transaksi::count();
        if ($totalTransaksi == 0) return [];

        $programs = T04Transaksi::select('id_program', DB::raw('count(*) as count'))
            ->groupBy('id_program')
            ->get();

        $result = [];
        foreach ($programs as $prog) {
            $nama = T03ProgramWakaf::where('id_program', $prog->id_program)->value('nama_program');
            $persenan = ($prog->count / $totalTransaksi) * 100;
            $result[] = [
                'nama_program' => $nama,
                'persen' => round($persenan, 2)
            ];
        }
        return $result;
    }

    public function getTrendWakafPerTahun($tahun)
    {
        T04Transaksi::sweepExpiredTransactions();

        $driver = DB::connection()->getDriverName();
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
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw($monthQuery))
            ->orderBy('bulan')
            ->get();
    }
}
