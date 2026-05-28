<?php

namespace App\Repositories\Nazhir;

use App\Models\T03ProgramWakaf;
use App\Models\T04Transaksi;
use App\RepositoryInterfaces\Nazhir\NazhirProgramRepositoryInterface;
use Illuminate\Support\Facades\DB;

class NazhirProgramRepository implements NazhirProgramRepositoryInterface
{
    public function getListProgram(array $filters = [])
    {
        $programs = T03ProgramWakaf::all();
        foreach ($programs as $prog) {
            $sum = T04Transaksi::where('id_program', $prog->id_program)
                ->where('status_pembayaran', 1)
                ->sum('nominal') ?? 0;
            if ($prog->dana_terkumpul != $sum) {
                $prog->dana_terkumpul = $sum;
                $prog->save();
            }
        }

        $query = T03ProgramWakaf::with('t05_laporan_penyalurans:id_laporan,id_program')->select(
            'id_program',
            'nama_program',
            'deskripsi',
            'target_dana',
            'dana_terkumpul',
            DB::raw('CASE WHEN target_dana > 0 THEN ROUND((dana_terkumpul / target_dana) * 100, 2) ELSE 0 END as progress'),
            'due_date',
            'status_program as status',
            'gambar_thumbnail'
        );

        if (!empty($filters['search'])) {
            $query->where('nama_program', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['sort']) && in_array($filters['sort'], ['asc', 'desc'])) {
            $query->orderBy('nama_program', $filters['sort']);
        } else {
            $query->orderBy('id_program', 'desc');
        }

        if (!empty($filters['all'])) {
            return $query->get();
        }

        $limit = $filters['limit'] ?? 10;
        return $query->paginate($limit);
    }

    public function createProgram(array $data)
    {
        return T03ProgramWakaf::create($data);
    }

    public function updateProgram($id, array $data)
    {
        return T03ProgramWakaf::where('id_program', $id)->update($data);
    }

    public function deleteProgram($id)
    {
        DB::table('t05_laporan_penyaluran')->where('id_program', $id)->delete();
        DB::table('t04_transaksi')->where('id_program', $id)->delete();
        return T03ProgramWakaf::where('id_program', $id)->delete();
    }
}
