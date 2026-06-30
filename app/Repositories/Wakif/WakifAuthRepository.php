<?php

namespace App\Repositories\Wakif;

use App\Models\T02User;
use App\RepositoryInterfaces\Wakif\WakifAuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class WakifAuthRepository implements WakifAuthRepositoryInterface
{
    public function findByEmail(string $email)
    {
        // assume id_role for wakif could be 2 or null, but let's just find by email for now
        return T02User::where('email', $email)->first();
    }

    public function createWakif(array $data)
    {
        return T02User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'password' => Hash::make($data['password']),
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'id_role' => 2 // assuming 2 is Wakif
        ]);
    }

    public function updatePassword(int $userId, string $newPassword)
    {
        return T02User::where('id_user', $userId)->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
