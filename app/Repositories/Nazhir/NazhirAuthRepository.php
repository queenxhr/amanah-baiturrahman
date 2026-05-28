<?php

namespace App\Repositories\Nazhir;

use App\Models\T02User;
use App\RepositoryInterfaces\Nazhir\NazhirAuthRepositoryInterface;

class NazhirAuthRepository implements NazhirAuthRepositoryInterface
{
    public function findByEmail(string $email)
    {
        return T02User::where('email', $email)->first(); // We can restrict to id_role = 1 if defined
    }
}
