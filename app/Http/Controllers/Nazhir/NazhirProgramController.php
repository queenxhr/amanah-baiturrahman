<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Services\Nazhir\NazhirProgramService;
use Illuminate\Http\Request;

class NazhirProgramController extends Controller
{
    protected $service;

    public function __construct(NazhirProgramService $service)
    {
        $this->service = $service;
    }

    public function getListProgram(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'limit' => $request->query('limit', 10),
            'page' => $request->query('page', 1),
            'all' => $request->query('all'),
            'sort' => $request->query('sort', 'asc')
        ];
        $data = $this->service->getListProgram($filters);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function createProgram(Request $request)
    {
        $data = $request->validate([
            'nama_program'  => 'required|string|max:150',
            'deskripsi'     => 'nullable|string',
            'target_dana'   => 'required|numeric|min:0',
            'dana_terkumpul'=> 'nullable|numeric|min:0',
            'status_program'=> 'nullable|integer|in:0,1,2,3',
            'due_date'      => 'nullable|date',
            'gambar_thumbnail' => 'nullable|image|max:5120',
        ]);
        $data['status_program'] = 2; // Default to Pending Review (Ditinjau)
        if ($request->hasFile('gambar_thumbnail')) {
            $file = $request->file('gambar_thumbnail');
            $path = $file->store('programs', 'public');
            $data['gambar_thumbnail'] = '/storage/' . $path;
        }

        $this->service->createProgram($data);
        return response()->json(['success' => true, 'message' => 'Program berhasil ditambahkan'], 201);
    }

    public function updateProgram(Request $request, $id)
    {
        $data = $request->validate([
            'nama_program'  => 'sometimes|string|max:150',
            'deskripsi'     => 'sometimes|string',
            'target_dana'   => 'sometimes|numeric|min:0',
            'dana_terkumpul'=> 'sometimes|numeric|min:0',
            'status_program'=> 'sometimes|integer|in:0,1,2,3',
            'due_date'      => 'sometimes|date',
            'gambar_thumbnail' => 'nullable|image|max:5120',
        ]);
        if ($request->hasFile('gambar_thumbnail')) {
            $file = $request->file('gambar_thumbnail');
            $path = $file->store('programs', 'public');
            $data['gambar_thumbnail'] = '/storage/' . $path;
        }

        $this->service->updateProgram($id, $data);
        return response()->json(['success' => true, 'message' => 'Program berhasil diupdate']);
    }

    public function deleteProgram($id)
    {
        $this->service->deleteProgram($id);
        return response()->json(['success' => true, 'message' => 'Program berhasil dihapus']);
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('editor', 'public');
            $url = '/storage/' . $path;
            return response()->json(['success' => true, 'url' => $url]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }
}
