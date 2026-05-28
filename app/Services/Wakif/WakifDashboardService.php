<?php

namespace App\Services\Wakif;

use App\RepositoryInterfaces\Wakif\WakifDashboardRepositoryInterface;

class WakifDashboardService
{
    protected $repo;

    public function __construct(WakifDashboardRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getCounters($bulan = null, $tahun = null)
    {
        return $this->repo->getCounters($bulan, $tahun);
    }

    public function getProgramWakafList()
    {
        $programs = $this->repo->getProgramWakafList();
        // Shorten description to 1 sentence
        foreach ($programs as $prog) {
            $sentences = explode('.', $prog->deskripsi);
            $prog->deskripsi = isset($sentences[0]) ? $sentences[0] . '.' : $prog->deskripsi;
        }
        return $programs;
    }

    public function getProgramById($id)
    {
        return $this->repo->getProgramById($id);
    }

    public function getProgramDeskripsi($id)
    {
        return $this->repo->getProgramDeskripsi($id);
    }

    public function getCounterDonatur($idProgram = null)
    {
        return ['counter_donatur' => $this->repo->getCounterDonatur($idProgram)];
    }

    public function getProgramCountdown($id)
    {
        return $this->repo->getProgramCountdown($id);
    }

    public function getDonaturByProgram($idProgram, array $filters)
    {
        return $this->repo->getDonaturByProgram($idProgram, $filters);
    }

    public function getBeritaLaporan($idProgram = null)
    {
        return $this->repo->getBeritaLaporan($idProgram);
    }

    public function getPenyebaranProgram($bulan = null, $tahun = null)
    {
        return $this->repo->getPenyebaranProgram($bulan, $tahun);
    }

    public function getTrendWakafPerTahun($tahun)
    {
        return $this->repo->getTrendWakafPerTahun($tahun);
    }
}
