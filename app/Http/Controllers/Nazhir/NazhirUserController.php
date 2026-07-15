<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Models\T02User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NazhirUserController extends Controller
{
    public function getListUser(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', 'asc');
        if (!in_array(strtolower($sort), ['asc', 'desc'])) {
            $sort = 'asc';
        }

        // Only role 2 (Wakif)
        $query = T02User::where('id_role', 2);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%')
                  ->orWhere('no_hp', 'ilike', '%' . $search . '%');
            });
        }

        $query->orderBy('created_at', $sort);

        if ($request->query('all')) {
            $users = $query->get()->map(function ($user) {
                $totalWakaf = DB::table('t04_transaksi')
                    ->where('id_user', $user->id_user)
                    ->where('status_pembayaran', 1)
                    ->sum('nominal') ?? 0;

                return [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'alamat' => $user->alamat,
                    'tanggal_lahir' => $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : null,
                    'created_at' => $user->created_at,
                    'role' => 'Wakif',
                    'total_wakaf' => $totalWakaf
                ];
            });

            return response()->json(['success' => true, 'data' => $users]);
        }

        $limit = $request->query('limit', 10);
        $paginator = $query->paginate($limit);

        $paginator->getCollection()->transform(function ($user) {
            $totalWakaf = DB::table('t04_transaksi')
                ->where('id_user', $user->id_user)
                ->where('status_pembayaran', 1)
                ->sum('nominal') ?? 0;

            return [
                'id_user' => $user->id_user,
                'nama' => $user->nama,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'jenis_kelamin' => $user->jenis_kelamin,
                'alamat' => $user->alamat,
                'tanggal_lahir' => $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : null,
                'created_at' => $user->created_at,
                'role' => 'Wakif',
                'total_wakaf' => $totalWakaf
            ];
        });

        return response()->json(['success' => true, 'data' => $paginator]);
    }
}
