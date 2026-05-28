<?php

namespace Database\Factories;

use App\Models\T02User;
use App\Models\T01Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class T02UserFactory extends Factory
{
    protected $model = T02User::class;

    public function definition(): array
    {
        return [
            'id_role' => T01RoleFactory::new(),
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('P@ssword123'),
            'no_hp' => $this->faker->phoneNumber(),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat' => $this->faker->address(),
            'tanggal_lahir' => $this->faker->date(),
            'created_at' => now(),
            'edited_at' => now(),
        ];
    }
}
