<?php

namespace App\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use App\Services\Pos\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salesSummary(Request $request, ReportService $reportService, int $outletId)
    {
        return response()->json($reportService->salesSummary($outletId, '', ''), 501);
    }
}
