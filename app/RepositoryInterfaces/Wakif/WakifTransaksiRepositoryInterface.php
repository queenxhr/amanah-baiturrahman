<?php

namespace App\RepositoryInterfaces\Wakif;

interface WakifTransaksiRepositoryInterface
{
    public function createTransaksi(array $data);
    public function getDetailPembayaran($id);
    public function getRiwayatTransaksi($userId);
    public function getTransaksiById($id);
}
