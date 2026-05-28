<?php

namespace App\Http\Controllers\Nazhir;

use App\Http\Controllers\Controller;
use App\Services\Nazhir\NazhirDashboardService;
use Illuminate\Http\Request;

class NazhirDashboardController extends Controller
{
    protected $service;

    public function __construct(NazhirDashboardService $service)
    {
        $this->service = $service;
    }

    public function getCounters(Request $request)
    {
        $filters = [
            'start' => $request->query('start'),
            'end' => $request->query('end'),
            'program' => $request->query('program'),
            'bulan' => $request->query('bulan'),
            'tahun' => $request->query('tahun')
        ];
        $data = $this->service->getCounters($filters);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPenyebaranProgram()
    {
        $data = $this->service->getPenyebaranProgram();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getTrendWakafPerTahun(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $data = $this->service->getTrendWakafPerTahun($tahun);
        return response()->json(['success' => true, 'data' => $data]);
    }
}
