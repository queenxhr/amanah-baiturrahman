<?php

namespace App\Repositories\Nazhir;

use App\Models\T02User;
use App\RepositoryInterfaces\Nazhir\NazhirAuthRepositoryInterface;

use Illuminate\Support\Facades\Hash;

class NazhirAuthRepository implements NazhirAuthRepositoryInterface
{
    public function findByEmail(string $email)
    {
        return T02User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first(); // We can restrict to id_role = 1 if defined
    }

    public function createNazhir(array $data)
    {
        return T02User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'password' => Hash::make($data['password']),
            'id_role' => 1,
            'status' => 'pending'
        ]);
    }
}
