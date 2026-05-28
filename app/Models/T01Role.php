<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class T01Role
 * 
 * @property int $id_role
 * @property string $nama_role
 * @property Carbon|null $created_at
 * @property Carbon|null $edited_at
 * 
 * @property Collection|T02User[] $t02_users
 *
 * @package App\Models
 */
class T01Role extends Model
{
	protected $table = 't01_roles';
	protected $primaryKey = 'id_role';
	public $timestamps = false;

	protected $casts = [
		'edited_at' => 'datetime'
	];

	protected $fillable = [
		'nama_role',
		'edited_at'
	];

	public function t02_users()
	{
		return $this->hasMany(T02User::class, 'id_role');
	}
}
