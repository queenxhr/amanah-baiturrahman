<?php

namespace App\Services\Wakif;

use App\RepositoryInterfaces\Wakif\WakifTransaksiRepositoryInterface;

class WakifTransaksiService
{
    protected $repo;

    public function __construct(WakifTransaksiRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function createTransaksiGuest(array $data)
    {
        // nama, id_program, nominal, bukti_pembayaran, pesan_doa
        $data['kode_referensi'] = 'INV-G-' . time();
        return $this->repo->createTransaksi($data);
    }

    public function createTransaksiUser(array $data, int $userId)
    {
        // id_user, nama (auto from user), id_program, nominal, bukti_pembayaran, pesan_doa
        $user = \App\Models\T02User::find($userId);
        $data['id_user'] = $userId;
        $data['nama'] = $user ? $user->nama : ($data['nama'] ?? '');
        $data['kode_referensi'] = 'INV-U-' . time();
        return $this->repo->createTransaksi($data);
    }

    public function getDetailPembayaran($id)
    {
        return $this->repo->getDetailPembayaran($id);
    }

    public function getRiwayatTransaksi($userId)
    {
        return $this->repo->getRiwayatTransaksi($userId);
    }

    public function getTransaksiById($id)
    {
        return $this->repo->getTransaksiById($id);
    }
}
