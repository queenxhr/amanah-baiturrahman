<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSuperadmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;

        // 1. Check Sanctum Bearer token for API requests
        if ($request->bearerToken() || $request->headers->has('Authorization')) {
            $user = Auth::guard('sanctum')->user();
        }

        // 2. Fallback to session
        if (!$user && session()->has('superadmin_user_id')) {
            $user = \App\Models\T02User::find(session('superadmin_user_id'));
        }

        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Silakan login sebagai Superadmin.'], 401);
            }
            return redirect('/superadmin/login')->with('error', 'Akses ditolak. Silakan login sebagai Superadmin.');
        }

        if ((int)$user->id_role !== 3) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Peran Anda bukan Superadmin.'], 403);
            }
            return redirect('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Superadmin.');
        }

        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
