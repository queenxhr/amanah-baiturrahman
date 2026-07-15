<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Models\T06PencairanDana;
use App\Models\T03ProgramWakaf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class NazhirPencairanController extends Controller
{
    public function getAvailableFunds(Request $request)
    {
        $request->validate([
            'id_program' => 'required|integer'
        ]);

        try {
            $program = T03ProgramWakaf::findOrFail($request->id_program);
            
            $totalApprovedPencairan = T06PencairanDana::where('id_program', $program->id_program)
                ->where('status_pencairan', 1)
                ->sum('jumlah_dana');

            $availableFunds = $program->dana_terkumpul - $totalApprovedPencairan;
            if ($availableFunds < 0) {
                $availableFunds = 0;
            }

            return response()->json([
                'success' => true,
                'available_funds' => $availableFunds
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user && session()->has('nazhir_user_id')) {
                $user = \App\Models\T02User::find(session('nazhir_user_id'));
            }

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $limit = $request->query('limit', 10);
            $paginator = T06PencairanDana::with(['program', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            $paginator->getCollection()->transform(function($p) {
                $suratUrl = null;
                if (!empty($p->surat_approval)) {
                    $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
                    $suratUrl = \Illuminate\Support\Facades\Storage::disk($disk)->url($p->surat_approval);
                }
                return [
                    'id_pencairan' => $p->id_pencairan,
                    'id_program' => $p->id_program,
                    'nama_program' => $p->program ? $p->program->nama_program : 'Unknown',
                    'nama_nazhir' => $p->user ? $p->user->nama : 'Unknown',
                    'jumlah_dana' => $p->jumlah_dana,
                    'keterangan' => $p->keterangan,
                    'status_pencairan' => $p->status_pencairan,
                    'surat_approval' => $p->surat_approval,
                    'surat_approval_url' => $suratUrl,
                    'created_at' => $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $p->updated_at ? $p->updated_at->format('Y-m-d H:i:s') : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $paginator
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program' => 'required|integer',
            'jumlah_dana' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            if (!$user && session()->has('nazhir_user_id')) {
                $user = \App\Models\T02User::find(session('nazhir_user_id'));
            }

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $program = T03ProgramWakaf::findOrFail($request->id_program);

            // Requirement 1: status_program must be 1 (Active/Approved)
            if ((int)$program->status_program !== 1) {
                throw new Exception("Pencairan dana hanya dapat diajukan untuk program wakaf yang aktif/telah disetujui.", 400);
            }

            // Requirement 2: dana_terkumpul must be >= 10% of target_dana
            $minimumTarget = $program->target_dana * 0.1;
            if ($program->dana_terkumpul < $minimumTarget) {
                throw new Exception("Dana terkumpul belum mencapai batas minimal 10% dari target dana program (Min: Rp " . number_format($minimumTarget) . ").", 400);
            }

            // Requirement 3: Check remaining available funds
            $totalApprovedPencairan = T06PencairanDana::where('id_program', $program->id_program)
                ->where('status_pencairan', 1)
                ->sum('jumlah_dana');

            $availableFunds = $program->dana_terkumpul - $totalApprovedPencairan;
            if ($request->jumlah_dana > $availableFunds) {
                throw new Exception("Jumlah dana yang diajukan melebihi sisa dana terkumpul yang tersedia untuk dicairkan (Tersedia: Rp " . number_format($availableFunds) . ").", 400);
            }

            $pencairan = T06PencairanDana::create([
                'id_program' => $request->id_program,
                'id_user' => $user->id_user,
                'jumlah_dana' => $request->jumlah_dana,
                'keterangan' => $request->keterangan,
                'status_pencairan' => 0 // Pending
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pencairan dana berhasil dikirim.',
                'data' => $pencairan
            ], 201);

        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int) $e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }
}
