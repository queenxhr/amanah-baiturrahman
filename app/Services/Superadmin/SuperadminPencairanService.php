<?php

namespace App\Services\Superadmin;

use App\RepositoryInterfaces\Superadmin\SuperadminPencairanRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class SuperadminPencairanService
{
    protected $repo;

    public function __construct(SuperadminPencairanRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListPencairan(array $filters = [])
    {
        return $this->repo->getListPencairan($filters);
    }

    public function approvePencairan($id)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if ((int)$pencairan->status_pencairan !== 0) {
            throw new Exception("Pengajuan pencairan tidak dalam status menunggu persetujuan (pending).");
        }

        if (empty($pencairan->surat_approval)) {
            throw new Exception("Surat persetujuan belum diupload. Silakan upload surat terlebih dahulu sebelum menyetujui.");
        }

        $this->repo->updatePencairanStatus($id, 1); // Approved
        return true;
    }

    public function rejectPencairan($id)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if ((int)$pencairan->status_pencairan !== 0) {
            throw new Exception("Pengajuan pencairan tidak dalam status menunggu persetujuan (pending).");
        }

        $this->repo->updatePencairanStatus($id, 2); // Rejected
        return true;
    }

    public function downloadSuratFormat($id)
    {
        $pencairan = $this->repo->findPencairanById($id);

        $terbilang = $this->terbilang((int)$pencairan->jumlah_dana);

        $namaNazhir = $pencairan->user ? $pencairan->user->nama : 'Nazhir';
        $qrBase64 = null;
        try {
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($namaNazhir);
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 3, // 3 seconds timeout
                ]
            ]);
            $qrData = @file_get_contents($qrUrl, false, $ctx);
            if ($qrData) {
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
            }
        } catch (\Exception $e) {
            // fallback to null
        }

        $pdf = Pdf::loadView('pdf.surat_pencairan', [
            'pencairan' => $pencairan,
            'terbilang' => $terbilang,
            'qrCode' => $qrBase64,
        ])->setOption('isRemoteEnabled', true);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'Surat-Pencairan-' . str_pad($id, 4, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    public function uploadSuratApproval($id, $file)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if ((int)$pencairan->status_pencairan !== 0) {
            throw new Exception("Hanya pengajuan yang masih pending yang dapat diupload suratnya.");
        }

        $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
        $path = $file->store('surat_pencairan', $disk);

        $this->repo->uploadSuratApproval($id, $path);
        return $path;
    }

    public function getSuratUrl($id)
    {
        $pencairan = $this->repo->findPencairanById($id);
        if (empty($pencairan->surat_approval)) {
            throw new Exception("Surat persetujuan belum diupload.");
        }

        $disk = (empty(config('filesystems.disks.azure.key')) && empty(config('filesystems.disks.azure.connection_string'))) ? 'public' : 'azure';
        return Storage::disk($disk)->url($pencairan->surat_approval);
    }

    /**
     * Convert integer to Indonesian words (terbilang)
     */
    private function terbilang(int $n): string
    {
        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
                   'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
                   'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];

        if ($n < 20) {
            return $satuan[$n];
        } elseif ($n < 100) {
            return $satuan[intval($n / 10)] . ' puluh' . ($n % 10 ? ' ' . $satuan[$n % 10] : '');
        } elseif ($n < 200) {
            return 'seratus' . ($n % 100 ? ' ' . $this->terbilang($n % 100) : '');
        } elseif ($n < 1000) {
            return $satuan[intval($n / 100)] . ' ratus' . ($n % 100 ? ' ' . $this->terbilang($n % 100) : '');
        } elseif ($n < 2000) {
            return 'seribu' . ($n % 1000 ? ' ' . $this->terbilang($n % 1000) : '');
        } elseif ($n < 1000000) {
            return $this->terbilang(intval($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . $this->terbilang($n % 1000) : '');
        } elseif ($n < 1000000000) {
            return $this->terbilang(intval($n / 1000000)) . ' juta' . ($n % 1000000 ? ' ' . $this->terbilang($n % 1000000) : '');
        } elseif ($n < 1000000000000) {
            return $this->terbilang(intval($n / 1000000000)) . ' miliar' . ($n % 1000000000 ? ' ' . $this->terbilang($n % 1000000000) : '');
        }
        return (string)$n;
    }
}
