<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirProgramRepositoryInterface
{
    public function getListProgram(array $filters = []);
    public function createProgram(array $data);
    public function updateProgram($id, array $data);
    public function deleteProgram($id);
}
