<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirLaporanRepositoryInterface
{
    public function getStatusLaporan($idProgram);
    public function getLaporanByProgram($idProgram);
    public function getLaporanById($idLaporan);
    public function createLaporan(array $data);
    public function updateLaporan($idLaporan, array $data);
}
