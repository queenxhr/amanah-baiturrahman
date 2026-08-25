<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirDashboardRepositoryInterface;

class NazhirDashboardService
{
    protected $repo;

    public function __construct(NazhirDashboardRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getCounters(array $filters)
    {
        return $this->repo->getCounters($filters);
    }

    public function getPenyebaranProgram($bulan = null, $tahun = null)
    {
        return $this->repo->getPenyebaranProgram($bulan, $tahun);
    }

    public function getTrendWakafPerTahun($tahun, $bulan = null, $programId = null)
    {
        return $this->repo->getTrendWakafPerTahun($tahun, $bulan, $programId);
    }
}
