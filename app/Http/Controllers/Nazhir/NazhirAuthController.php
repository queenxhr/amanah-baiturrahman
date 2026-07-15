<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Services\Nazhir\NazhirAuthService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class NazhirAuthController extends Controller
{
    protected $service;

    public function __construct(NazhirAuthService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => strtolower($request->input('email'))]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $data = $this->service->login($request->all());
            Auth::login($data['user']);
            session(['nazhir_user_id' => $data['user']->id_user]);
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $data
            ]);
        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int) $e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function register(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => strtolower($request->input('email'))]);
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'unique:t02_users,email',
                function ($attribute, $value, $fail) {
                    if (app()->runningUnitTests()) {
                        return;
                    }
                    $domain = substr(strrchr($value, "@"), 1);
                    if ($domain && !checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                        $fail('Domain email tidak valid atau fiktif.');
                    }
                }
            ],
            'no_hp' => 'required|string|max:15|unique:t02_users,no_hp',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
        ], [
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain.',
            'no_hp.required' => 'Kolom nomor handphone wajib diisi.',
            'no_hp.unique' => 'Nomor handphone sudah digunakan. Silakan gunakan nomor lain.',
            'jenis_kelamin.required' => 'Kolom jenis kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Format jenis kelamin tidak valid.',
            'tanggal_lahir.required' => 'Kolom tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'password.required' => 'Kolom kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kolom kata sandi harus minimal 8 karakter.',
            'password.mixed' => 'Kolom kata sandi harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
            'password.letters' => 'Kolom kata sandi harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Kolom kata sandi harus mengandung setidaknya satu angka.',
            'password.symbols' => 'Kolom kata sandi harus mengandung setidaknya satu simbol.',
        ]);

        try {
            $data = $this->service->register($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil. Silakan menunggu persetujuan dari Superadmin sebelum masuk.',
                'data' => $data
            ], 201);
        } catch (Exception $e) {
            $status = (is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? (int) $e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $token = $request->user()->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }
        }
        session()->forget('nazhir_user_id');
        Auth::guard('web')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}
