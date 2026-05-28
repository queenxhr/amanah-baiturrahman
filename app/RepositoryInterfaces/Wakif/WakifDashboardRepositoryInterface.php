<?php

namespace App\RepositoryInterfaces\Wakif;

interface WakifDashboardRepositoryInterface
{
    public function getCounters($bulan = null, $tahun = null);
    public function getProgramWakafList();
    public function getProgramById($id);
    public function getProgramDeskripsi($id);
    public function getCounterDonatur($idProgram = null);
    public function getProgramCountdown($id);
    public function getDonaturByProgram($idProgram, array $filters);
    public function getBeritaLaporan($idProgram = null);
    public function getPenyebaranProgram($bulan = null, $tahun = null);
    public function getTrendWakafPerTahun($tahun);
}
