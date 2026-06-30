<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class T06PencairanDana extends Model
{
    protected $table = 't06_pencairan_dana';
    protected $primaryKey = 'id_pencairan';

    protected $casts = [
        'jumlah_dana' => 'float',
        'status_pencairan' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'id_program',
        'id_user',
        'jumlah_dana',
        'keterangan',
        'status_pencairan'
    ];

    public function program()
    {
        return $this->belongsTo(T03ProgramWakaf::class, 'id_program');
    }

    public function user()
    {
        return $this->belongsTo(T02User::class, 'id_user');
    }
}
