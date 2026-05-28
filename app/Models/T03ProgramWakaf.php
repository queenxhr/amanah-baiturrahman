<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class T03ProgramWakaf
 * 
 * @property int $id_program
 * @property string $nama_program
 * @property string|null $deskripsi
 * @property float $target_dana
 * @property float|null $dana_terkumpul
 * @property int|null $status_program
 * @property Carbon|null $created_at
 * @property Carbon|null $edited_at
 * @property Carbon|null $due_date
 * 
 * @property Collection|T04Transaksi[] $t04_transaksis
 * @property Collection|T05LaporanPenyaluran[] $t05_laporan_penyalurans
 *
 * @package App\Models
 */
class T03ProgramWakaf extends Model
{
	protected $table = 't03_program_wakaf';
	protected $primaryKey = 'id_program';
	public $timestamps = false;

	protected $casts = [
		'target_dana' => 'float',
		'dana_terkumpul' => 'float',
		'status_program' => 'int',
		'edited_at' => 'datetime',
		'due_date' => 'datetime'
	];

	protected $fillable = [
		'nama_program',
		'deskripsi',
		'target_dana',
		'dana_terkumpul',
		'status_program',
		'edited_at',
		'due_date',
		'gambar_thumbnail'
	];

	public function t04_transaksis()
	{
		return $this->hasMany(T04Transaksi::class, 'id_program');
	}

	public function t05_laporan_penyalurans()
	{
		return $this->hasMany(T05LaporanPenyaluran::class, 'id_program');
	}
}
