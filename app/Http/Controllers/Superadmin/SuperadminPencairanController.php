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
                'limit'  => $request->query('limit', 10),
            ];

            $pencairanList = $this->service->getListPencairan($filters);

            $pencairanList->getCollection()->transform(function($p) {
                $suratUrl = null;
                if (!empty($p->surat_approval)) {
                    $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
                    $suratUrl = \Illuminate\Support\Facades\Storage::disk($disk)->url($p->surat_approval);
                }
                return [
                    'id_pencairan' => $p->id_pencairan,
                    'id_program' => $p->id_program,
                    'nama_program' => $p->program ? $p->program->nama_program : 'Unknown',
                    'id_user' => $p->id_user,
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
                'data' => $pencairanList
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

    public function downloadSurat($id)
    {
        try {
            return $this->service->downloadSuratFormat($id);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function uploadSurat(Request $request, $id)
    {
        $request->validate([
            'surat_approval' => 'required|file|mimes:pdf,jpeg,jpg,png|max:10240'
        ], [
            'surat_approval.required' => 'File surat persetujuan wajib diupload.',
            'surat_approval.mimes' => 'Format file harus PDF, JPG, atau PNG.',
            'surat_approval.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $this->service->uploadSuratApproval($id, $request->file('surat_approval'));
            return response()->json([
                'success' => true,
                'message' => 'Surat persetujuan berhasil diupload.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
