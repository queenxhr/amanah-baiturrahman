<?php

namespace App\Repositories\Nazhir;

use App\Models\T05LaporanPenyaluran;
use App\RepositoryInterfaces\Nazhir\NazhirLaporanRepositoryInterface;

class NazhirLaporanRepository implements NazhirLaporanRepositoryInterface
{
    public function getStatusLaporan($idProgram)
    {
        return T05LaporanPenyaluran::where('id_program', $idProgram)->exists();
    }

    public function getLaporanByProgram($idProgram)
    {
        return T05LaporanPenyaluran::where('id_program', $idProgram)
            ->orderBy('created_at', 'desc')
            ->select('id_laporan', 'id_program', 'judul_laporan', 'keterangan', 'dana_disalurkan', 'penerima_manfaat', 'created_at')
            ->get();
    }

    public function getLaporanById($idLaporan)
    {
        return T05LaporanPenyaluran::where('id_laporan', $idLaporan)->first();
    }

    public function createLaporan(array $data)
    {
        return T05LaporanPenyaluran::create($data);
    }

    public function updateLaporan($idLaporan, array $data)
    {
        return T05LaporanPenyaluran::where('id_laporan', $idLaporan)->update($data);
    }
}
