<?php

namespace App\Http\Controllers\Wakif;

use App\Http\Controllers\Controller;
use App\Services\Wakif\WakifUserService;
use Illuminate\Http\Request;

class WakifUserController extends Controller
{
    protected $service;

    public function __construct(WakifUserService $service)
    {
        $this->service = $service;
    }

    public function getProfile(Request $request)
    {
        $data = $this->service->getProfile($request->user()->id_user);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:t02_users,email,' . $request->user()->id_user . ',id_user',
            'no_hp' => 'sometimes|string|max:15|unique:t02_users,no_hp,' . $request->user()->id_user . ',id_user',
            'jenis_kelamin' => 'sometimes|string|max:1',
            'alamat' => 'sometimes|string',
            'tanggal_lahir' => 'sometimes|date'
        ], [
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain.',
            'no_hp.unique' => 'Nomor handphone sudah digunakan. Silakan gunakan nomor lain.',
        ]);

        $data = $this->service->updateProfile($request->user()->id_user, $request->all());
        
        return response()->json(['success' => true, 'message' => 'Profile updated successfully', 'data' => $data]);
    }
}
