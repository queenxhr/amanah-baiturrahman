<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirLaporanRepositoryInterface;

class NazhirLaporanService
{
    protected $repo;

    public function __construct(NazhirLaporanRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getStatusLaporan($idProgram)
    {
        return [
            'id_program'       => $idProgram,
            'laporan_tersedia' => $this->repo->getStatusLaporan($idProgram)
        ];
    }

    public function getLaporanByProgram($idProgram)
    {
        return $this->repo->getLaporanByProgram($idProgram);
    }

    public function getLaporanById($idLaporan)
    {
        return $this->repo->getLaporanById($idLaporan);
    }

    public function createLaporan(array $data)
    {
        $this->repo->createLaporan($data);
        return true;
    }

    public function updateLaporan($idLaporan, array $data)
    {
        $this->repo->updateLaporan($idLaporan, $data);
        return true;
    }
}
