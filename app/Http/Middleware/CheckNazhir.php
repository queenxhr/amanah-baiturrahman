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
        $user = Auth::user();
        if (!$user && session()->has('wakif_user_id')) {
            $user = \App\Models\T02User::find(session('wakif_user_id'));
        }

        if (!$user) {
            return redirect('/login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Nazhir.');
        }

        if ((int)$user->id_role !== 1) {
            return redirect('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Nazhir.');
        }

        return $next($request);
    }
}
