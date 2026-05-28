<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class T05LaporanPenyaluran
 * 
 * @property int $id_laporan
 * @property int|null $id_program
 * @property string $judul_laporan
 * @property string|null $keterangan
 * @property float $dana_disalurkan
 * @property Carbon|null $created_at
 * @property Carbon|null $edited_at
 * @property int|null $penerima_manfaat
 * 
 * @property T03ProgramWakaf|null $t03_program_wakaf
 *
 * @package App\Models
 */
class T05LaporanPenyaluran extends Model
{
	protected $table = 't05_laporan_penyaluran';
	protected $primaryKey = 'id_laporan';
	public $timestamps = false;

	protected $casts = [
		'id_program' => 'int',
		'dana_disalurkan' => 'float',
		'edited_at' => 'datetime',
		'penerima_manfaat' => 'int'
	];

	protected $fillable = [
		'id_program',
		'judul_laporan',
		'keterangan',
		'dana_disalurkan',
		'edited_at',
		'penerima_manfaat',
		'gambar_laporan'
	];

	public function t03_program_wakaf()
	{
		return $this->belongsTo(T03ProgramWakaf::class, 'id_program');
	}
}
