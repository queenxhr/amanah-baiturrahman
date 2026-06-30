<?php

namespace App\RepositoryInterfaces\Nazhir;

interface NazhirAuthRepositoryInterface
{
    public function findByEmail(string $email);
    public function createNazhir(array $data);
}
