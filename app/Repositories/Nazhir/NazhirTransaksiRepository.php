<?php

namespace App\Repositories\Nazhir;

use App\Models\T04Transaksi;
use App\RepositoryInterfaces\Nazhir\NazhirTransaksiRepositoryInterface;

class NazhirTransaksiRepository implements NazhirTransaksiRepositoryInterface
{
    public function getListTransaksi(array $filters)
    {
        $sort = (!empty($filters['sort']) && in_array(strtolower($filters['sort']), ['asc', 'desc'])) ? strtolower($filters['sort']) : 'desc';

        $query = T04Transaksi::with('t03_program_wakaf:id_program,nama_program')
            ->select('id_transaksi', 'id_program', 'nama as nama_donatur', 'nominal', 'created_at', 'bukti_pembayaran', 'status_pembayaran', 'kode_referensi', 'metode_pembayaran')
            ->orderBy('created_at', $sort);

        if (!empty($filters['start'])) {
            $query->where('created_at', '>=', $filters['start'] . ' 00:00:00');
        }

        if (!empty($filters['end'])) {
            $query->where('created_at', '<=', $filters['end'] . ' 23:59:59');
        }

        if (!empty($filters['program'])) {
            $query->where('id_program', $filters['program']);
        }

        $limit = $filters['limit'] ?? 10;
        return $query->paginate($limit);
    }

    public function getBuktiPembayaran($id)
    {
        return T04Transaksi::select('id_transaksi', 'bukti_pembayaran')->where('id_transaksi', $id)->first();
    }

    public function approve($id, $status)
    {
        $transaksi = T04Transaksi::find($id);
        if ($transaksi) {
            $transaksi->status_pembayaran = $status;
            $saved = $transaksi->save();

            if ($saved) {
                $program = \App\Models\T03ProgramWakaf::find($transaksi->id_program);
                if ($program) {
                    $sum = T04Transaksi::where('id_program', $program->id_program)
                        ->where('status_pembayaran', 1)
                        ->sum('nominal') ?? 0;
                    $program->dana_terkumpul = $sum;
                    if ($sum >= $program->target_dana) {
                        $program->status_program = 0; // Selesai
                    }
                    $program->save();
                }
            }
            return $saved;
        }
        return false;
    }

    public function getAllTransaksiForExport(array $filters)
    {
        $sort = (!empty($filters['sort']) && in_array(strtolower($filters['sort']), ['asc', 'desc'])) ? strtolower($filters['sort']) : 'desc';

        $query = T04Transaksi::with('t03_program_wakaf:id_program,nama_program')
            ->select('id_transaksi', 'id_program', 'nama as nama_donatur', 'nominal', 'created_at', 'status_pembayaran', 'kode_referensi', 'metode_pembayaran')
            ->orderBy('created_at', $sort);

        if (!empty($filters['start'])) {
            $query->where('created_at', '>=', $filters['start'] . ' 00:00:00');
        }

        if (!empty($filters['end'])) {
            $query->where('created_at', '<=', $filters['end'] . ' 23:59:59');
        }

        if (!empty($filters['program'])) {
            $query->where('id_program', $filters['program']);
        }

        return $query->get();
    }
}
