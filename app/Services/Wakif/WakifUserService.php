<?php

namespace App\Services\Wakif;

use App\RepositoryInterfaces\Wakif\WakifUserRepositoryInterface;

class WakifUserService
{
    protected $repo;

    public function __construct(WakifUserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getProfile($userId)
    {
        return $this->repo->getProfile($userId);
    }

    public function updateProfile($userId, array $data)
    {
        $this->repo->updateProfile($userId, $data);
        return $this->getProfile($userId);
    }
}
