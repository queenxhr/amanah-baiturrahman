<?php

namespace App\Repositories\Superadmin;

use App\Models\T02User;
use App\RepositoryInterfaces\Superadmin\SuperadminUserRepositoryInterface;

class SuperadminUserRepository implements SuperadminUserRepositoryInterface
{
    public function getListUsers(array $filters = [])
    {
        $query = T02User::with('t01_role')->whereIn('id_role', [1, 2]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%')
                  ->orWhere('no_hp', 'ilike', '%' . $search . '%');
            });
        }

        if (!empty($filters['role'])) {
            $query->where('id_role', (int)$filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['all'])) {
            return $query->orderBy('created_at', 'desc')->get();
        }

        $limit = $filters['limit'] ?? 10;
        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    public function findUserById($id)
    {
        return T02User::findOrFail($id);
    }

    public function updateUserStatus($id, string $status)
    {
        $user = $this->findUserById($id);
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function deleteUser($id)
    {
        $user = $this->findUserById($id);
        return $user->delete();
    }
}
