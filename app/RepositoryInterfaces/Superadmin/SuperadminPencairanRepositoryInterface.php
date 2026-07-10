<?php

namespace App\RepositoryInterfaces\Superadmin;

interface SuperadminPencairanRepositoryInterface
{
    public function getListPencairan(array $filters = []);
    public function findPencairanById($id);
    public function updatePencairanStatus($id, int $status);
    public function uploadSuratApproval($id, string $suratPath);
}
