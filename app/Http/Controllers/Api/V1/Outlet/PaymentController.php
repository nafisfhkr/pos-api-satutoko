<?php

namespace App\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayCashRequest;
use App\Http\Requests\PayQrisRequest;
use App\Models\Sale;
use App\Services\Pos\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payCash(PayCashRequest $request, PaymentService $paymentService, int $outletId, int $saleId)
    {
        $sale = Sale::where('outlet_id', $outletId)->findOrFail($saleId);
        $userId = $request->user()?->id;

        try {
            $payment = $paymentService->payCash(
                $sale,
                (float) $request->input('cash_received'),
                $userId
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran cash berhasil',
            'data' => [
                'payment' => $payment,
                'sale' => $sale->fresh(['items', 'payments']),
            ],
        ]);
    }

    public function payQris(PayQrisRequest $request, PaymentService $paymentService, int $outletId, int $saleId)
    {
        $sale = Sale::where('outlet_id', $outletId)->findOrFail($saleId);
        $userId = $request->user()?->id;
        $idempotencyKey = $request->header('Idempotency-Key');

        try {
            $payment = $paymentService->payQris(
                $sale,
                $request->input('reference_no'),
                $idempotencyKey,
                $userId
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran QRIS berhasil',
            'data' => [
                'payment' => $payment,
                'sale' => $sale->fresh(['items', 'payments']),
            ],
        ]);
    }
}
