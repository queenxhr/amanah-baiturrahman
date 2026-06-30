<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Services\Nazhir\NazhirLaporanService;
use Illuminate\Http\Request;

class NazhirLaporanController extends Controller
{
    protected $service;

    public function __construct(NazhirLaporanService $service)
    {
        $this->service = $service;
    }

    public function getStatusLaporan($programId)
    {
        $data = $this->service->getStatusLaporan($programId);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getLaporanByProgram($programId)
    {
        $data = $this->service->getLaporanByProgram($programId);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getLaporanById($id)
    {
        $data = $this->service->getLaporanById($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function createLaporan(Request $request)
    {
        $data = $request->validate([
            'id_program'      => 'required|integer|exists:t03_program_wakaf,id_program',
            'judul_laporan'   => 'required|string|max:150',
            'keterangan'      => 'nullable|string',
            'dana_disalurkan' => 'required|numeric',
            'penerima_manfaat'=> 'nullable|integer|min:0',
            'gambar_laporan'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('gambar_laporan')) {
            $path = $request->file('gambar_laporan')->store('reports', 'public');
            $data['gambar_laporan'] = '/storage/' . $path;
        }

        $this->service->createLaporan($data);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil ditambahkan'], 201);
    }

    public function updateLaporan(Request $request, $id)
    {
        $data = $request->validate([
            'id_program'      => 'sometimes|integer',
            'judul_laporan'   => 'sometimes|string|max:150',
            'keterangan'      => 'sometimes|string',
            'dana_disalurkan' => 'sometimes|numeric',
            'penerima_manfaat'=> 'sometimes|integer|min:0',
            'gambar_laporan'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('gambar_laporan')) {
            $path = $request->file('gambar_laporan')->store('reports', 'public');
            $data['gambar_laporan'] = '/storage/' . $path;
        }

        $this->service->updateLaporan($id, $data);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil diupdate']);
    }

    public function deleteLaporan($id)
    {
        $this->service->deleteLaporan($id);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus']);
    }
}
