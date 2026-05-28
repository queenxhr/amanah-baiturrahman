<?php

namespace App\Services\Wakif;

use App\RepositoryInterfaces\Wakif\WakifAuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Exception;

class WakifAuthService
{
    protected $repo;

    public function __construct(WakifAuthRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function login(array $data)
    {
        $user = $this->repo->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception("Invalid credentials", 401);
        }

        // Assuming Sanctum is used
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function register(array $data)
    {
        $user = $this->repo->findByEmail($data['email']);
        if ($user) {
            throw new Exception("Email already exists", 400);
        }

        $newUser = $this->repo->createWakif($data);
        
        $token = $newUser->createToken('auth_token')->plainTextToken;

        return [
            'user' => $newUser,
            'token' => $token
        ];
    }

    public function updatePassword(int $userId, array $data)
    {
        // Actually we need the user model to check old password, but userId is fine if we query again
        // Or find the user first
        $user = auth()->user(); // from Auth guard
        
        if (!Hash::check($data['old_password'], $user->password)) {
            throw new Exception("Old password does not match", 400);
        }

        $this->repo->updatePassword($userId, $data['new_password']);
        return true;
    }
}
