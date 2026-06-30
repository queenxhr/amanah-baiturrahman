<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = null;
        $path = $request->getPathInfo();

        if (str_starts_with($path, '/superadmin')) {
            if (session()->has('superadmin_user_id')) {
                $user = \App\Models\T02User::find(session('superadmin_user_id'));
            }
        } elseif (
            str_starts_with($path, '/nazhir') || 
            $path === '/dashboard' || 
            str_starts_with($path, '/manajemen-') || 
            str_starts_with($path, '/laporan/tambah') || 
            (str_starts_with($path, '/laporan/') && str_ends_with($path, '/edit')) ||
            (str_starts_with($path, '/program/') && str_ends_with($path, '/edit'))
        ) {
            if (session()->has('nazhir_user_id')) {
                $user = \App\Models\T02User::find(session('nazhir_user_id'));
            }
        } else {
            // Default to Wakif
            if (session()->has('wakif_user_id')) {
                $user = \App\Models\T02User::find(session('wakif_user_id'));
            }
        }

        // No fallback to $request->user() to prevent cross-role session leaks

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
