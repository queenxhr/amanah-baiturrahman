<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirDashboardRepositoryInterface
{
    public function getCounters(array $filters);
    public function getPenyebaranProgram($bulan = null, $tahun = null);
    public function getTrendWakafPerTahun($tahun, $bulan = null, $programId = null);
}
