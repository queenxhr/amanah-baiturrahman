<?php

namespace Database\Factories;

use App\Models\T03ProgramWakaf;
use Illuminate\Database\Eloquent\Factories\Factory;

class T03ProgramWakafFactory extends Factory
{
    protected $model = T03ProgramWakaf::class;

    public function definition(): array
    {
        return [
            'nama_program' => $this->faker->sentence(3),
            'deskripsi' => $this->faker->paragraph(),
            'target_dana' => $this->faker->numberBetween(10000000, 100000000),
            'dana_terkumpul' => $this->faker->numberBetween(0, 10000000),
            'status_program' => 1, // Active by default
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'created_at' => now(),
            'edited_at' => now(),
        ];
    }
}
