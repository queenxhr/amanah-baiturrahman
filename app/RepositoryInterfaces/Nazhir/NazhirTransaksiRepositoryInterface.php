<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirTransaksiRepositoryInterface
{
    public function getListTransaksi(array $filters);
    public function getBuktiPembayaran($id);
    public function approve($id, $status);
    public function getAllTransaksiForExport(array $filters);
}
