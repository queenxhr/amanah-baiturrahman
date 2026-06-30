<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\SuperadminPencairanService;
use Illuminate\Http\Request;
use Exception;

class SuperadminPencairanController extends Controller
{
    protected $service;

    public function __construct(SuperadminPencairanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'status' => $request->query('status'),
            ];

            $pencairanList = $this->service->getListPencairan($filters);

            $pencairans = $pencairanList->map(function($p) {
                return [
                    'id_pencairan' => $p->id_pencairan,
                    'id_program' => $p->id_program,
                    'nama_program' => $p->program ? $p->program->nama_program : 'Unknown',
                    'id_user' => $p->id_user,
                    'nama_nazhir' => $p->user ? $p->user->nama : 'Unknown',
                    'jumlah_dana' => $p->jumlah_dana,
                    'keterangan' => $p->keterangan,
                    'status_pencairan' => $p->status_pencairan,
                    'created_at' => $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $p->updated_at ? $p->updated_at->format('Y-m-d H:i:s') : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pencairans
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $this->service->approvePencairan($id);
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pencairan dana berhasil disetujui.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function reject($id)
    {
        try {
            $this->service->rejectPencairan($id);
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pencairan dana telah ditolak.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
