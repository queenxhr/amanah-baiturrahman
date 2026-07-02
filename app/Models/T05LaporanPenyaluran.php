<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

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

	public function getGambarLaporanAttribute($value)
	{
		if (empty($value)) {
			return null;
		}

		if (filter_var($value, FILTER_VALIDATE_URL) || str_starts_with($value, 'http')) {
			return $value;
		}

		$cleanPath = preg_replace('/^\/?storage\//', '', $value);

		if (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) {
			return Storage::disk('public')->url($cleanPath);
		}

		return Storage::disk('azure')->url($cleanPath);
	}

	public function t03_program_wakaf()
	{
		return $this->belongsTo(T03ProgramWakaf::class, 'id_program');
	}
}
