<?php

namespace App\Repositories\Wakif;

use App\Models\T04Transaksi;
use App\RepositoryInterfaces\Wakif\WakifTransaksiRepositoryInterface;

class WakifTransaksiRepository implements WakifTransaksiRepositoryInterface
{
    public function createTransaksi(array $data)
    {
        return T04Transaksi::create($data);
    }

    public function getDetailPembayaran($id)
    {
        // id_transaksi, nama, id_program, nama program, created_at, nominal
        return T04Transaksi::with('t03_program_wakaf:id_program,nama_program')
            ->select('id_transaksi', 'nama', 'id_program', 'created_at', 'nominal', 'kode_referensi', 'metode_pembayaran')
            ->where('id_transaksi', $id)
            ->first();
    }

    public function getRiwayatTransaksi($userId)
    {
        // id_transaksi, nama program, nominal, tanggal, status_pembayaran
        return T04Transaksi::with('t03_program_wakaf:id_program,nama_program')
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->select('id_transaksi', 'id_program', 'nominal', 'created_at', 'status_pembayaran', 'kode_referensi')
            ->get();
    }

    public function getTransaksiById($id)
    {
        // id_transaksi, nama program, nama donatur, nominal, tanggal, status_pembayaran
        return T04Transaksi::with('t03_program_wakaf:id_program,nama_program')
            ->select('id_transaksi', 'id_program', 'nama as nama_donatur', 'nominal', 'created_at', 'status_pembayaran', 'kode_referensi', 'metode_pembayaran')
            ->where('id_transaksi', $id)
            ->first();
    }

    public function updateBuktiPembayaran(int $id, string $url)
    {
        $transaksi = T04Transaksi::find($id);
        if ($transaksi) {
            $transaksi->bukti_pembayaran = $url;
            $transaksi->save();
        }
        return $transaksi;
    }
}
