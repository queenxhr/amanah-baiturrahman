<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirDashboardRepositoryInterface
{
    public function getCounters(array $filters);
    public function getPenyebaranProgram();
    public function getTrendWakafPerTahun($tahun);
}
