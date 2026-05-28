<?php

namespace App\RepositoryInterfaces\Wakif;

interface WakifUserRepositoryInterface
{
    public function getProfile($userId);
    public function updateProfile($userId, array $data);
}
