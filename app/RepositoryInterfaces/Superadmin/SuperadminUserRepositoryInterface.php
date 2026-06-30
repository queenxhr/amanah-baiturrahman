<?php

namespace App\RepositoryInterfaces\Superadmin;

interface SuperadminUserRepositoryInterface
{
    public function getListUsers(array $filters = []);
    public function findUserById($id);
    public function updateUserStatus($id, string $status);
    public function deleteUser($id);
}
