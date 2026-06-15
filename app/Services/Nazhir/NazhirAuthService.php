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

        $token = $user->createToken('nazhir_auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
