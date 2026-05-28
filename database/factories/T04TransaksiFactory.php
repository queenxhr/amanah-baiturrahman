<?php

namespace Database\Factories;

use App\Models\T04Transaksi;
use App\Models\T02User;
use App\Models\T03ProgramWakaf;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class T04TransaksiFactory extends Factory
{
    protected $model = T04Transaksi::class;

    public function definition(): array
    {
        return [
            'id_user' => T02UserFactory::new(),
            'nama' => $this->faker->name(), // Could be null if guest, but setting a name
            'pesan_doa' => $this->faker->sentence(),
            'id_program' => T03ProgramWakafFactory::new(),
            'nominal' => $this->faker->numberBetween(10000, 1000000),
            'bukti_pembayaran' => 'bukti_' . Str::random(10) . '.png',
            'status_pembayaran' => $this->faker->randomElement([0, 1, 2]), // 0: pending, 1: approved, 2: rejected
            'kode_referensi' => Str::random(10),
            'created_at' => now(),
            'edited_at' => now(),
        ];
    }
}
