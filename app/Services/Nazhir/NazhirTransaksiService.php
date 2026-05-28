<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirTransaksiRepositoryInterface;

class NazhirTransaksiService
{
    protected $repo;

    public function __construct(NazhirTransaksiRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListTransaksi(array $filters)
    {
        return $this->repo->getListTransaksi($filters);
    }

    public function getBuktiPembayaran($id)
    {
        return $this->repo->getBuktiPembayaran($id);
    }

    public function approve($id, $status)
    {
        $this->repo->approve($id, $status);
        return true;
    }

    public function getAllTransaksiForExport(array $filters)
    {
        return $this->repo->getAllTransaksiForExport($filters);
    }
}
