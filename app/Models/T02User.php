<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * Class T02User
 * 
 * @property int $id_user
 * @property int|null $id_role
 * @property string $nama
 * @property string $email
 * @property string $password
 * @property string $no_hp
 * @property string|null $jenis_kelamin
 * @property string|null $alamat
 * @property Carbon|null $tanggal_lahir
 * @property Carbon|null $created_at
 * @property Carbon|null $edited_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * 
 * @property T01Role|null $t01_role
 * @property Collection|T04Transaksi[] $t04_transaksis
 *
 * @package App\Models
 */
class T02User extends Authenticatable
{
	use HasApiTokens, Notifiable, HasFactory, TwoFactorAuthenticatable;

	protected $table = 't02_users';
	protected $primaryKey = 'id_user';
	public $timestamps = false;

	protected $casts = [
		'id_role' => 'int',
		'tanggal_lahir' => 'datetime',
		'edited_at' => 'datetime',
		'two_factor_confirmed_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token',
		'two_factor_secret'
	];

	protected $fillable = [
		'id_role',
		'nama',
		'email',
		'password',
		'no_hp',
		'jenis_kelamin',
		'alamat',
		'tanggal_lahir',
		'status',
		'edited_at',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'two_factor_confirmed_at'
	];

	public function t01_role()
	{
		return $this->belongsTo(T01Role::class, 'id_role');
	}

	public function t04_transaksis()
	{
		return $this->hasMany(T04Transaksi::class, 'id_user');
	}
}
