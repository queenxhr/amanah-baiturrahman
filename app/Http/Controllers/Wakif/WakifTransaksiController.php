<?php

namespace App\Http\Controllers\Wakif;

use App\Http\Controllers\Controller;
use App\Services\Wakif\WakifTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WakifTransaksiController extends Controller
{
    protected $service;

    public function __construct(WakifTransaksiService $service)
    {
        $this->service = $service;
    }

    public function createTransaksiGuest(Request $request)
    {
        // nama, no_hp, email, id_program, nominal, bukti_pembayaran, pesan_doa, hide_nama
        $request->validate([
            'nama'             => 'required|string|max:100',
            'no_hp'            => 'required|string|max:20',
            'email'            => 'required|email|max:100',
            'id_program'       => 'required|integer|exists:t03_program_wakaf,id_program',
            'nominal'          => 'required|numeric|min:10000',
            'pesan_doa'        => 'nullable|string',
            'hide_nama'        => 'nullable|integer|in:0,1',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'metode_pembayaran'=> 'nullable|string|in:QRIS,BCA,qris,bca'
        ]);

        $data = $request->except('bukti_pembayaran');
        $data['hide_nama'] = $data['hide_nama'] ?? 0;

        if ($request->hasFile('bukti_pembayaran')) {
            $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', $disk);
            $data['bukti_pembayaran'] = $path;
        }

        $transaksi = $this->service->createTransaksiGuest($data);
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function createTransaksiUser(Request $request)
    {
        // id_user, nama (auto), email, id_program, nominal, bukti_pembayaran, pesan_doa, hide_nama
        $request->validate([
            'email'            => 'required|email|max:100',
            'id_program'       => 'required|integer|exists:t03_program_wakaf,id_program',
            'nominal'          => 'required|numeric|min:10000',
            'pesan_doa'        => 'nullable|string',
            'hide_nama'        => 'nullable|integer|in:0,1',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'metode_pembayaran'=> 'nullable|string|in:QRIS,BCA,qris,bca'
        ]);

        $data = $request->except('bukti_pembayaran');
        $data['hide_nama'] = $data['hide_nama'] ?? 0;
        // nama auto-filled from user account in service

        if ($request->hasFile('bukti_pembayaran')) {
            $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', $disk);
            $data['bukti_pembayaran'] = $path;
        }

        $transaksi = $this->service->createTransaksiUser($data, $request->user()->id_user);
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', $disk);
            
            $transaksi = $this->service->uploadBuktiPembayaran($id, $path);
            return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil diupload', 'data' => $transaksi]);
        }

        return response()->json(['success' => false, 'message' => 'File bukti pembayaran tidak ditemukan'], 400);
    }

    public function getDetailPembayaran($id)
    {
        $data = $this->service->getDetailPembayaran($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getRiwayatTransaksi(Request $request)
    {
        $data = $this->service->getRiwayatTransaksi($request->user()->id_user);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getTransaksiById($id)
    {
        $data = $this->service->getTransaksiById($id);
        return response()->json(['success' => true, 'data' => $data]);
    }
}
