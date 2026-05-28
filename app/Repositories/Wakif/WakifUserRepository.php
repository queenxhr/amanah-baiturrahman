<?php

namespace App\Repositories\Wakif;

use App\Models\T02User;
use App\RepositoryInterfaces\Wakif\WakifUserRepositoryInterface;

class WakifUserRepository implements WakifUserRepositoryInterface
{
    public function getProfile($userId)
    {
        return T02User::select('nama', 'email', 'no_hp', 'jenis_kelamin', 'alamat', 'tanggal_lahir')
            ->where('id_user', $userId)
            ->first();
    }

    public function updateProfile($userId, array $data)
    {
        return T02User::where('id_user', $userId)->update($data);
    }
}
