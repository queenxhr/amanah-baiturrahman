<?php

namespace App\Services\Superadmin;

use App\RepositoryInterfaces\Superadmin\SuperadminPencairanRepositoryInterface;
use Exception;

class SuperadminPencairanService
{
    protected $repo;

    public function __construct(SuperadminPencairanRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListPencairan(array $filters = [])
    {
        return $this->repo->getListPencairan($filters);
    }

    public function approvePencairan($id)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if ((int)$pencairan->status_pencairan !== 0) {
            throw new Exception("Pengajuan pencairan tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updatePencairanStatus($id, 1); // Approved
        return true;
    }

    public function rejectPencairan($id)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if ((int)$pencairan->status_pencairan !== 0) {
            throw new Exception("Pengajuan pencairan tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updatePencairanStatus($id, 2); // Rejected
        return true;
    }
}
