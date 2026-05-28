<?php

namespace App\Http\Controllers\Wakif;

use App\Http\Controllers\Controller;
use App\Services\Wakif\WakifAuthService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class WakifAuthController extends Controller
{
    protected $service;

    public function __construct(WakifAuthService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $data = $this->service->login($request->all());
            Auth::login($data['user']);
            session(['wakif_user_id' => $data['user']->id_user]);
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $data
            ]);
        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int)$e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email',
            'no_hp' => 'required|string|max:15',
            'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
        ], [
            'password.required' => 'Kolom kata sandi wajib diisi.',
            'password.min' => 'Kolom kata sandi harus minimal 8 karakter.',
            'password.mixed' => 'Kolom kata sandi harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
            'password.letters' => 'Kolom kata sandi harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Kolom kata sandi harus mengandung setidaknya satu angka.',
            'password.symbols' => 'Kolom kata sandi harus mengandung setidaknya satu simbol.',
        ]);

        try {
            $data = $this->service->register($request->all());
            Auth::login($data['user']);
            session(['wakif_user_id' => $data['user']->id_user]);
            return response()->json([
                'success' => true,
                'message' => 'Register successful',
                'data' => $data
            ], 201);
        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int)$e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
        ], [
            'new_password.required' => 'Kolom kata sandi baru wajib diisi.',
            'new_password.min' => 'Kolom kata sandi baru harus minimal 8 karakter.',
            'new_password.mixed' => 'Kolom kata sandi baru harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
            'new_password.letters' => 'Kolom kata sandi baru harus mengandung setidaknya satu huruf.',
            'new_password.numbers' => 'Kolom kata sandi baru harus mengandung setidaknya satu angka.',
            'new_password.symbols' => 'Kolom kata sandi baru harus mengandung setidaknya satu simbol.',
        ]);

        try {
            $this->service->updatePassword($request->user()->id_user, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Password updated successful'
            ]);
        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int)$e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        session()->forget('wakif_user_id');
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}
