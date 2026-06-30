<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\T02User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class SuperadminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $user = T02User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new Exception("Kata sandi salah. Silakan coba lagi", 401);
            }

            if ((int)$user->id_role !== 3) {
                throw new Exception("Akses ditolak. Akun Anda tidak terdaftar sebagai Superadmin.", 403);
            }

            if ($user->status === 'blocked') {
                throw new Exception("Akun Anda telah diblokir.", 403);
            }

            Auth::login($user);
            session(['superadmin_user_id' => $user->id_user]);
            
            $token = $user->createToken('superadmin_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
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
        session()->forget('superadmin_user_id');
        Auth::guard('web')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}
