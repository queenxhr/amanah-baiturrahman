<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckWakif
{
    /**
     * Handle an incoming request.
     * Protects Wakif-only routes (profil, riwayat-transaksi, transaksi detail).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;

        // 1. Check Sanctum Bearer token for API requests
        if ($request->bearerToken() || $request->headers->has('Authorization')) {
            $user = Auth::guard('sanctum')->user();
        }

        // 2. Fallback to session
        if (!$user && session()->has('wakif_user_id')) {
            $user = \App\Models\T02User::find(session('wakif_user_id'));
        }

        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Silakan login sebagai Wakif.'], 401);
            }
            return redirect('/wakif/login')->with('error', 'Akses ditolak. Silakan login sebagai Wakif.');
        }

        if ((int) $user->id_role !== 2) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan.'], 403);
            }
            return redirect('/wakif/login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Wakif.');
        }

        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
