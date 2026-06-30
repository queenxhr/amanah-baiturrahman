<?php

namespace App\RepositoryInterfaces\Superadmin;

interface SuperadminProgramRepositoryInterface
{
    public function getListPrograms(array $filters = []);
    public function findProgramById($id);
    public function updateProgramStatus($id, int $status);
}
