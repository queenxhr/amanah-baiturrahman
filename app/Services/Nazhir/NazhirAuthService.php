<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirAuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Exception;

class NazhirAuthService
{
    protected $repo;

    public function __construct(NazhirAuthRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function login(array $data)
    {
        $user = $this->repo->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception("Kata sandi salah. Silakan coba lagi", 401);
        }

        if ((int)$user->id_role !== 1) {
            throw new Exception("Akun Anda tidak terdaftar sebagai Nazhir.", 403);
        }

        if ($user->status === 'pending') {
            throw new Exception("Akun Anda sedang menunggu persetujuan dari Superadmin.", 403);
        }

        if ($user->status === 'blocked') {
            throw new Exception("Akun Anda telah diblokir oleh Superadmin.", 403);
        }

        $token = $user->createToken('nazhir_auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function register(array $data)
    {
        $user = $this->repo->findByEmail($data['email']);
        if ($user) {
            throw new Exception("Email sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.", 400);
        }

        $newUser = $this->repo->createNazhir($data);

        return [
            'user' => $newUser
        ];
    }
}
