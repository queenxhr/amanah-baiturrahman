<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class T04Transaksi
 * 
 * @property int $id_transaksi
 * @property int|null $id_user
 * @property string|null $nama
 * @property string|null $pesan_doa
 * @property int|null $id_program
 * @property float $nominal
 * @property string|null $bukti_pembayaran
 * @property int|null $status_pembayaran
 * @property string|null $kode_referensi
 * @property Carbon|null $created_at
 * @property Carbon|null $edited_at
 * @property string|null $no_hp
 * @property int|null $hide_nama
 * 
 * @property T02User|null $t02_user
 * @property T03ProgramWakaf|null $t03_program_wakaf
 *
 * @package App\Models
 */
class T04Transaksi extends Model
{
	protected $table = 't04_transaksi';
	protected $primaryKey = 'id_transaksi';
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'id_program' => 'int',
		'nominal' => 'float',
		'status_pembayaran' => 'int',
		'edited_at' => 'datetime',
		'hide_nama' => 'int'
	];

	protected $fillable = [
		'id_user',
		'nama',
		'pesan_doa',
		'id_program',
		'nominal',
		'bukti_pembayaran',
		'status_pembayaran',
		'kode_referensi',
		'edited_at',
		'no_hp',
		'hide_nama'
	];

	protected static function booted()
	{
		static::created(function ($transaksi) {
			if (empty($transaksi->kode_referensi)) {
				$transaksi->kode_referensi = 'WKF-' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT);
				$transaksi->saveQuietly();
			}
		});

		static::saved(function ($transaksi) {
			$transaksi->recalculateProgramFunds();
		});

		static::deleted(function ($transaksi) {
			$transaksi->recalculateProgramFunds();
		});
	}

	public function recalculateProgramFunds()
	{
		if ($this->id_program) {
			$sum = static::where('id_program', $this->id_program)
				->where('status_pembayaran', 1)
				->sum('nominal');

			T03ProgramWakaf::where('id_program', $this->id_program)
				->update(['dana_terkumpul' => $sum]);
		}
	}

	public function getKodeReferensiAttribute($value)
	{
		if (empty($value) && $this->id_transaksi) {
			return 'WKF-' . str_pad($this->id_transaksi, 6, '0', STR_PAD_LEFT);
		}
		return $value;
	}

	public function t02_user()
	{
		return $this->belongsTo(T02User::class, 'id_user');
	}

	public function t03_program_wakaf()
	{
		return $this->belongsTo(T03ProgramWakaf::class, 'id_program');
	}
}
