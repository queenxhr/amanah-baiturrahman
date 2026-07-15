<?php

namespace App\RepositoryInterfaces\Wakif;

interface WakifTransaksiRepositoryInterface
{
    public function createTransaksi(array $data);
    public function getDetailPembayaran($id);
    public function getRiwayatTransaksi($userId);
    public function getTransaksiById($id);
    public function updateBuktiPembayaran(int $id, string $url);
    public function cancelTransaksi(int $id);
}
