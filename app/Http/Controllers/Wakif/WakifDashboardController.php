<?php

namespace App\Http\Controllers\Wakif;

use App\Http\Controllers\Controller;
use App\Services\Wakif\WakifDashboardService;
use Illuminate\Http\Request;

class WakifDashboardController extends Controller
{
    protected $service;

    public function __construct(WakifDashboardService $service)
    {
        $this->service = $service;
    }

    public function getCounters(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $data = $this->service->getCounters($bulan, $tahun);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getProgramWakafList()
    {
        $data = $this->service->getProgramWakafList();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getProgramById($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID format'], 400);
        }
        $data = $this->service->getProgramById($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getProgramDeskripsi($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID format'], 400);
        }
        $data = $this->service->getProgramDeskripsi($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getCounterDonatur(Request $request)
    {
        $idProgram = $request->query('id_program');
        if ($idProgram !== null && !is_numeric($idProgram)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID format'], 400);
        }
        $data = $this->service->getCounterDonatur($idProgram);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getProgramCountdown($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID format'], 400);
        }
        $data = $this->service->getProgramCountdown($id);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getDonaturByProgram(Request $request, $id)
    {
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid ID format'], 400);
        }
        $filters = [
            'start' => $request->query('start'),
            'end' => $request->query('end'),
            'limit' => $request->query('limit', 10),
            'page' => $request->query('page', 1)
        ];
        $data = $this->service->getDonaturByProgram($id, $filters);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getBeritaLaporan(Request $request)
    {
        $idProgram = $request->query('id_program');
        $data = $this->service->getBeritaLaporan($idProgram);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPenyebaranProgram(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $data = $this->service->getPenyebaranProgram($bulan, $tahun);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getTrendWakafPerTahun(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $bulan = $request->query('bulan');
        $data = $this->service->getTrendWakafPerTahun($tahun, $bulan);
        return response()->json(['success' => true, 'data' => $data]);
    }
}
