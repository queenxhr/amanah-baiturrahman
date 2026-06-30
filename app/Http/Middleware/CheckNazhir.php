<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckNazhir
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
        if (!$user && session()->has('nazhir_user_id')) {
            $user = \App\Models\T02User::find(session('nazhir_user_id'));
        }

        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Silakan login sebagai Nazhir.'], 401);
            }
            return redirect('/nazhir/login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Nazhir.');
        }

        if ((int)$user->id_role !== 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Peran Anda bukan Nazhir.'], 403);
            }
            return redirect('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Nazhir.');
        }

        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
