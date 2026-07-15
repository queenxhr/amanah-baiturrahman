<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\SuperadminProgramService;
use Illuminate\Http\Request;
use Exception;

class SuperadminProgramController extends Controller
{
    protected $service;

    public function __construct(SuperadminProgramService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'limit'  => $request->query('limit', 10),
                'all'    => $request->query('all'),
            ];

            $programList = $this->service->getListPrograms($filters);

            if ($request->query('all')) {
                $programs = $programList->map(function($prog) {
                    return [
                        'id_program' => $prog->id_program,
                        'nama_program' => $prog->nama_program,
                        'deskripsi' => $prog->deskripsi,
                        'target_dana' => $prog->target_dana,
                        'dana_terkumpul' => $prog->dana_terkumpul ?? 0,
                        'status_program' => $prog->status_program,
                        'due_date' => $prog->due_date ? $prog->due_date->format('Y-m-d') : null,
                        'gambar_thumbnail' => $prog->gambar_thumbnail,
                        'created_at' => $prog->created_at
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $programs
                ]);
            }

            $programList->getCollection()->transform(function($prog) {
                return [
                    'id_program' => $prog->id_program,
                    'nama_program' => $prog->nama_program,
                    'deskripsi' => $prog->deskripsi,
                    'target_dana' => $prog->target_dana,
                    'dana_terkumpul' => $prog->dana_terkumpul ?? 0,
                    'status_program' => $prog->status_program,
                    'due_date' => $prog->due_date ? $prog->due_date->format('Y-m-d') : null,
                    'gambar_thumbnail' => $prog->gambar_thumbnail,
                    'created_at' => $prog->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $programList
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
            $this->service->approveProgram($id);
            return response()->json([
                'success' => true,
                'message' => 'Program wakaf berhasil disetujui dan diterbitkan.'
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
            $this->service->rejectProgram($id);
            return response()->json([
                'success' => true,
                'message' => 'Program wakaf berhasil ditolak.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
