<?php

namespace App\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleCreateRequest;
use App\Http\Requests\SaleItemRequest;
use App\Models\Sale;
use App\Services\Pos\SalesService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function store(SaleCreateRequest $request, SalesService $salesService)
    {
        $outletId = (int) $request->route('outletId');
        $userId = $request->user()?->id;

        $sale = $salesService->createSale($outletId, $userId, $request->input('note'));

        return response()->json([
            'success' => true,
            'message' => 'Sale DRAFT dibuat',
            'data' => $sale->load('items'),
        ], 201);
    }

    public function addItem(SaleItemRequest $request, SalesService $salesService, int $outletId, int $saleId)
    {
        $sale = Sale::where('outlet_id', $outletId)->findOrFail($saleId);

        try {
            $sale = $salesService->addItem(
                $sale,
                (int) $request->input('product_id'),
                (int) $request->input('qty'),
                $request->input('unit_price')
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item ditambahkan',
            'data' => $sale,
        ]);
    }

    public function show(Request $request, int $outletId, int $saleId)
    {
        $sale = Sale::with(['items.product', 'payments'])
            ->where('outlet_id', $outletId)
            ->findOrFail($saleId);

        return response()->json([
            'success' => true,
            'message' => 'Detail sale',
            'data' => $sale,
        ]);
    }
}
