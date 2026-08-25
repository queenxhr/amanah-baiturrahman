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
        // Shorten description to 1 sentence and strip HTML tags
        foreach ($programs as $prog) {
            $plainText = strip_tags($prog->deskripsi);
            $sentences = explode('.', $plainText);
            $prog->deskripsi = isset($sentences[0]) ? $sentences[0] . '.' : $plainText;
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

    public function getTrendWakafPerTahun($tahun, $bulan = null)
    {
        return $this->repo->getTrendWakafPerTahun($tahun, $bulan);
    }
}
