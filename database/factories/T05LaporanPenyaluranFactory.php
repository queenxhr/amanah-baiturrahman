<?php

namespace Database\Factories;

use App\Models\T05LaporanPenyaluran;
use App\Models\T03ProgramWakaf;
use Illuminate\Database\Eloquent\Factories\Factory;

class T05LaporanPenyaluranFactory extends Factory
{
    protected $model = T05LaporanPenyaluran::class;

    public function definition(): array
    {
        return [
            'id_program'      => T03ProgramWakafFactory::new(),
            'judul_laporan'   => $this->faker->sentence(4),
            'keterangan'      => $this->faker->paragraph(),
            'dana_disalurkan' => $this->faker->numberBetween(1000000, 5000000),
            'penerima_manfaat'=> $this->faker->numberBetween(10, 500),
            'created_at'      => now(),
            'edited_at'       => now(),
        ];
    }
}
