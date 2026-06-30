<?php

namespace App\Repositories\Superadmin;

use App\Models\T03ProgramWakaf;
use App\RepositoryInterfaces\Superadmin\SuperadminProgramRepositoryInterface;

class SuperadminProgramRepository implements SuperadminProgramRepositoryInterface
{
    public function getListPrograms(array $filters = [])
    {
        $query = T03ProgramWakaf::query();

        if (!empty($filters['search'])) {
            $query->where('nama_program', 'ilike', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status_program', (int)$filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findProgramById($id)
    {
        return T03ProgramWakaf::findOrFail($id);
    }

    public function updateProgramStatus($id, int $status)
    {
        $program = $this->findProgramById($id);
        $program->status_program = $status;
        $program->save();
        return $program;
    }
}
