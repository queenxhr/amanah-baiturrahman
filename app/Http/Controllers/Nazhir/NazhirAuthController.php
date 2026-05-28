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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $data = $this->service->login($request->all());
            Auth::login($data['user']);
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}
