<?php

namespace App\Services\Superadmin;

use App\RepositoryInterfaces\Superadmin\SuperadminUserRepositoryInterface;
use App\Mail\NazhirApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SuperadminUserService
{
    protected $repo;

    public function __construct(SuperadminUserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListUsers(array $filters = [])
    {
        return $this->repo->getListUsers($filters);
    }

    public function approveNazhir($id)
    {
        $user = $this->repo->findUserById($id);
        if ((int)$user->id_role !== 1) {
            throw new Exception("Hanya pendaftaran akun Nazhir yang memerlukan persetujuan.");
        }
        if ($user->status !== 'pending') {
            throw new Exception("Akun ini tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updateUserStatus($id, 'active');

        try {
            Mail::to($user->email)->send(new NazhirApprovedMail($user));
        } catch (\Exception $mailEx) {
            Log::error('Gagal mengirim email persetujuan Nazhir: ' . $mailEx->getMessage());
        }

        return true;
    }

    public function rejectNazhir($id)
    {
        $user = $this->repo->findUserById($id);
        if ((int)$user->id_role !== 1) {
            throw new Exception("Hanya pendaftaran akun Nazhir yang dapat ditolak.");
        }
        if ($user->status !== 'pending') {
            throw new Exception("Akun ini tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updateUserStatus($id, 'rejected');
        return true;
    }

    public function blockUser($id)
    {
        $user = $this->repo->findUserById($id);
        if ($user->status === 'blocked') {
            throw new Exception("Akun sudah diblokir.");
        }

        $this->repo->updateUserStatus($id, 'blocked');
        return true;
    }

    public function unblockUser($id)
    {
        $user = $this->repo->findUserById($id);
        if ($user->status !== 'blocked') {
            throw new Exception("Akun tidak sedang diblokir.");
        }

        $this->repo->updateUserStatus($id, 'active');
        return true;
    }

    public function deleteUser($id)
    {
        return $this->repo->deleteUser($id);
    }
}
