<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\SuperadminUserService;
use Illuminate\Http\Request;
use Exception;

class SuperadminUserController extends Controller
{
    protected $service;

    public function __construct(SuperadminUserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'role'   => $request->query('role'),
                'status' => $request->query('status'),
                'limit'  => $request->query('limit', 10),
                'all'    => $request->query('all'),
            ];

            $usersList = $this->service->getListUsers($filters);

            if ($request->query('all')) {
                $users = $usersList->map(function($user) {
                    return [
                        'id_user' => $user->id_user,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'no_hp' => $user->no_hp,
                        'status' => $user->status,
                        'id_role' => $user->id_role,
                        'role_name' => $user->t01_role ? $user->t01_role->nama_role : 'Unknown',
                        'created_at' => $user->created_at
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }

            $usersList->getCollection()->transform(function($user) {
                return [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'no_hp' => $user->no_hp,
                    'status' => $user->status,
                    'id_role' => $user->id_role,
                    'role_name' => $user->t01_role ? $user->t01_role->nama_role : 'Unknown',
                    'created_at' => $user->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $usersList
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $this->service->approveNazhir($id);
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran akun Nazhir berhasil disetujui.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function block($id)
    {
        try {
            $this->service->blockUser($id);
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil diblokir.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function unblock($id)
    {
        try {
            $this->service->unblockUser($id);
            return response()->json([
                'success' => true,
                'message' => 'Blokir akun berhasil dibuka.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function reject($id)
    {
        try {
            $this->service->rejectNazhir($id);
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran akun Nazhir berhasil ditolak.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deleteUser($id);
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
