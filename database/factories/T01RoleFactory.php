<?php

namespace Database\Factories;

use App\Models\T01Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class T01RoleFactory extends Factory
{
    protected $model = T01Role::class;

    public function definition(): array
    {
        return [
            'nama_role' => 'Role_' . $this->faker->unique()->randomNumber(),
            'created_at' => now(),
            'edited_at' => now(),
        ];
    }
}
