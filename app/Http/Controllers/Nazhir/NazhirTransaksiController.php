<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Services\Nazhir\NazhirTransaksiService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NazhirTransaksiController extends Controller
{
    protected $service;

    public function __construct(NazhirTransaksiService $service)
    {
        $this->service = $service;
    }

    public function getListTransaksi(Request $request)
    {
        $filters = [
            'start' => $request->query('start'),
            'end' => $request->query('end'),
            'program' => $request->query('program'),
            'sort' => $request->query('sort', 'desc'),
            'limit' => $request->query('limit', 10),
            'page' => $request->query('page', 1)
        ];
        $data = $this->service->getListTransaksi($filters);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getBuktiPembayaran($id)
    {
        $data = $this->service->getBuktiPembayaran($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'status_pembayaran' => 'required|integer'
        ]);

        $this->service->approve($id, $request->status_pembayaran);
        return response()->json(['success' => true, 'message' => 'Status pembayaran diupdate']);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = [
            'start' => $request->query('start'),
            'end'   => $request->query('end'),
            'program' => $request->query('program'),
            'sort'  => $request->query('sort', 'desc'),
        ];
        $transaksis = $this->service->getAllTransaksiForExport($filters);

        $filename = 'transaksi_wakif_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transaksis) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Header row
            fputcsv($handle, [
                'ID Transaksi',
                'Nama Donatur',
                'Program Wakaf',
                'Nominal',
                'Tanggal',
                'Metode Pembayaran',
                'Status Pembayaran',
            ]);
            foreach ($transaksis as $row) {
                $status = match((int)$row->status_pembayaran) {
                    0 => 'Menunggu',
                    1 => 'Berhasil',
                    2 => 'Gagal',
                    default => '-'
                };
                fputcsv($handle, [
                    $row->id_transaksi,
                    $row->nama_donatur,
                    $row->t03_program_wakaf->nama_program ?? '-',
                    $row->nominal,
                    $row->created_at,
                    strtoupper($row->metode_pembayaran ?? 'QRIS'),
                    $status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
