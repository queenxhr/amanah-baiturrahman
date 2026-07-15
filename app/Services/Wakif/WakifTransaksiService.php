<?php

namespace App\Services\Wakif;

use App\RepositoryInterfaces\Wakif\WakifTransaksiRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransaksiTagihanMail;
use App\Mail\TransaksiSuccessMail;

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
        $transaksi = $this->repo->createTransaksi($data);
        $transaksi->load('t03_program_wakaf');
        
        if ($transaksi->email) {
            Mail::to($transaksi->email)->send(new TransaksiTagihanMail($transaksi));
        }
        
        return $transaksi;
    }

    public function createTransaksiUser(array $data, int $userId)
    {
        // id_user, nama (auto from user), id_program, nominal, bukti_pembayaran, pesan_doa
        $user = \App\Models\T02User::find($userId);
        $data['id_user'] = $userId;
        $data['nama'] = $user ? $user->nama : ($data['nama'] ?? '');
        $data['kode_referensi'] = 'INV-U-' . time();
        $transaksi = $this->repo->createTransaksi($data);
        $transaksi->load('t03_program_wakaf');
        
        if ($transaksi->email) {
            Mail::to($transaksi->email)->send(new TransaksiTagihanMail($transaksi));
        }
        
        return $transaksi;
    }

    public function uploadBuktiPembayaran(int $id, string $buktiUrl)
    {
        $transaksi = $this->repo->updateBuktiPembayaran($id, $buktiUrl);
        $transaksi->load('t03_program_wakaf');
        
        if ($transaksi->email) {
            Mail::to($transaksi->email)->send(new TransaksiSuccessMail($transaksi));
        }
        
        return $transaksi;
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

    public function cancelTransaksi(int $id)
    {
        return $this->repo->cancelTransaksi($id);
    }
}
