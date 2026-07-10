<?php

namespace App\Repositories\Superadmin;

use App\Models\T06PencairanDana;
use App\RepositoryInterfaces\Superadmin\SuperadminPencairanRepositoryInterface;

class SuperadminPencairanRepository implements SuperadminPencairanRepositoryInterface
{
    public function getListPencairan(array $filters = [])
    {
        $query = T06PencairanDana::with(['program', 'user']);

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status_pencairan', (int)$filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findPencairanById($id)
    {
        return T06PencairanDana::with(['program', 'user'])->findOrFail($id);
    }

    public function updatePencairanStatus($id, int $status)
    {
        $pencairan = $this->findPencairanById($id);
        $pencairan->status_pencairan = $status;
        $pencairan->save();
        return $pencairan;
    }

    public function uploadSuratApproval($id, string $suratPath)
    {
        $pencairan = $this->findPencairanById($id);
        $pencairan->surat_approval = $suratPath;
        $pencairan->save();
        return $pencairan;
    }
}
