<?php

namespace App\Services\Superadmin;

use App\RepositoryInterfaces\Superadmin\SuperadminProgramRepositoryInterface;
use Exception;

class SuperadminProgramService
{
    protected $repo;

    public function __construct(SuperadminProgramRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListPrograms(array $filters = [])
    {
        return $this->repo->getListPrograms($filters);
    }

    public function approveProgram($id)
    {
        $program = $this->repo->findProgramById($id);
        if ((int)$program->status_program !== 2) {
            throw new Exception("Program tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updateProgramStatus($id, 1); // approved/active
        return true;
    }

    public function rejectProgram($id)
    {
        $program = $this->repo->findProgramById($id);
        if ((int)$program->status_program !== 2) {
            throw new Exception("Program tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updateProgramStatus($id, 3); // rejected
        return true;
    }
}
