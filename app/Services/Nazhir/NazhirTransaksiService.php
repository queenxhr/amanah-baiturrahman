<?php

namespace App\Services\Nazhir;

use App\RepositoryInterfaces\Nazhir\NazhirTransaksiRepositoryInterface;

class NazhirTransaksiService
{
    protected $repo;

    public function __construct(NazhirTransaksiRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListTransaksi(array $filters)
    {
        return $this->repo->getListTransaksi($filters);
    }

    public function getBuktiPembayaran($id)
    {
        return $this->repo->getBuktiPembayaran($id);
    }

    public function approve($id, $status)
    {
        $saved = $this->repo->approve($id, $status);
        
        if ($saved && $status == 1) {
            $transaksi = \App\Models\T04Transaksi::with('t03_program_wakaf')->find($id);
            if ($transaksi && $transaksi->email) {
                try {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['transaction' => $transaksi]);
                    $pdfContent = $pdf->output();
                    
                    \Illuminate\Support\Facades\Mail::to($transaksi->email)->send(
                        new \App\Mail\TransaksiApprovedMail($transaksi, $pdfContent)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send approval email for transaction ' . $id . ': ' . $e->getMessage());
                }
            }
        }
        
        return $saved;
    }

    public function getAllTransaksiForExport(array $filters)
    {
        return $this->repo->getAllTransaksiForExport($filters);
    }
}
