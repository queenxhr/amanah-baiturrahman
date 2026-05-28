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

    public function getPenyebaranProgram()
    {
        return $this->repo->getPenyebaranProgram();
    }

    public function getTrendWakafPerTahun($tahun)
    {
        return $this->repo->getTrendWakafPerTahun($tahun);
    }
}
