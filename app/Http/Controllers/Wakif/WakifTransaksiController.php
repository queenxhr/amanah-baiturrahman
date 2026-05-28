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
        // nama, no_hp, id_program, nominal, bukti_pembayaran, pesan_doa, hide_nama
        $request->validate([
            'nama'             => 'required|string|max:100',
            'no_hp'            => 'required|string|max:20',
            'id_program'       => 'required|integer|exists:t03_program_wakaf,id_program',
            'nominal'          => 'required|numeric|min:10000',
            'pesan_doa'        => 'nullable|string',
            'hide_nama'        => 'nullable|integer|in:0,1',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $data = $request->except('bukti_pembayaran');
        $data['hide_nama'] = $data['hide_nama'] ?? 0;

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = Storage::disk('public')->url($path);
        }

        $transaksi = $this->service->createTransaksiGuest($data);
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function createTransaksiUser(Request $request)
    {
        // id_user, nama (auto), id_program, nominal, bukti_pembayaran, pesan_doa, hide_nama
        $request->validate([
            'id_program'       => 'required|integer|exists:t03_program_wakaf,id_program',
            'nominal'          => 'required|numeric|min:10000',
            'pesan_doa'        => 'nullable|string',
            'hide_nama'        => 'nullable|integer|in:0,1',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $data = $request->except('bukti_pembayaran');
        $data['hide_nama'] = $data['hide_nama'] ?? 0;
        // nama auto-filled from user account in service

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = Storage::disk('public')->url($path);
        }

        $transaksi = $this->service->createTransaksiUser($data, $request->user()->id_user);
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
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
