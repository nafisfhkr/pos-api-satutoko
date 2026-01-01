<?php

namespace App\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use App\Services\Pos\ShiftService;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function open(Request $request, ShiftService $shiftService, int $outletId)
    {
        return response()->json($shiftService->open($outletId, $request->user()?->id ?? 0, 0), 501);
    }

    public function close(Request $request, ShiftService $shiftService, int $outletId, int $shiftId)
    {
        return response()->json($shiftService->close($shiftId, 0), 501);
    }
}
