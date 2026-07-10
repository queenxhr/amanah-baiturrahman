<?php

namespace App\RepositoryInterfaces\Wakif;

interface WakifAuthRepositoryInterface
{
    public function findByEmail(string $email);
    public function createWakif(array $data);
    public function verifyEmail(int $userId);
    public function updatePassword(int $userId, string $newPassword);
}
