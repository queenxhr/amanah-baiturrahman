<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirProgramRepositoryInterface;

class NazhirProgramService
{
    protected $repo;

    public function __construct(NazhirProgramRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListProgram(array $filters = [])
    {
        return $this->repo->getListProgram($filters);
    }

    public function createProgram(array $data)
    {
        // progress and due_date logic can be auto-handled, we just need basic ones
        $this->repo->createProgram($data);
        return true;
    }

    public function updateProgram($id, array $data)
    {
        $this->repo->updateProgram($id, $data);
        return true;
    }

    public function deleteProgram($id)
    {
        $this->repo->deleteProgram($id);
        return true;
    }
}
