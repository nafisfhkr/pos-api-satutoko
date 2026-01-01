<?php

namespace App\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustRequest;
use App\Models\Stock;
use App\Services\Pos\InventoryService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $outletId = (int) $request->route('outletId');

        $stocks = Stock::with('product')
            ->where('outlet_id', $outletId)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar stok outlet',
            'data' => $stocks,
        ]);
    }

    public function adjust(StockAdjustRequest $request, InventoryService $inventoryService)
    {
        $outletId = (int) $request->route('outletId');
        $userId = $request->user()?->id;

        $stock = $inventoryService->adjustStock(
            $outletId,
            (int) $request->input('product_id'),
            (int) $request->input('qty'),
            $request->input('reason'),
            $userId
        );

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan',
            'data' => $stock->load('product'),
        ], 201);
    }
}
