<?php

namespace App\Services\Pos;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function payCash(Sale $sale, float $cashReceived, ?int $userId = null): Payment
    {
        return DB::transaction(function () use ($sale, $cashReceived, $userId) {
            $sale->refresh();

            if ($sale->status !== Sale::STATUS_DRAFT) {
                throw new \RuntimeException('Sale sudah dibayar atau tidak valid.');
            }

            if ($cashReceived < $sale->total) {
                throw new \RuntimeException('Uang tunai tidak cukup.');
            }

            $this->lockAndValidateStock($sale);

            $payment = Payment::create([
                'sale_id' => $sale->id,
                'method' => Payment::METHOD_CASH,
                'amount' => $sale->total,
                'cash_received' => $cashReceived,
                'change_amount' => $cashReceived - $sale->total,
                'user_id' => $userId,
            ]);

            $this->finalizeSale($sale);
            $this->deductStockForSale($sale, $userId);

            return $payment->fresh();
        });
    }

    public function payQris(Sale $sale, string $referenceNo, ?string $idempotencyKey, ?int $userId = null): Payment
    {
        if ($idempotencyKey) {
            $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($sale, $referenceNo, $idempotencyKey, $userId) {
            $sale->refresh();

            if ($sale->status !== Sale::STATUS_DRAFT) {
                throw new \RuntimeException('Sale sudah dibayar atau tidak valid.');
            }

            $this->lockAndValidateStock($sale);

            $payment = Payment::create([
                'sale_id' => $sale->id,
                'method' => Payment::METHOD_QRIS,
                'amount' => $sale->total,
                'reference_no' => $referenceNo,
                'idempotency_key' => $idempotencyKey,
                'user_id' => $userId,
            ]);

            $this->finalizeSale($sale);
            $this->deductStockForSale($sale, $userId);

            return $payment->fresh();
        });
    }

    private function lockAndValidateStock(Sale $sale): void
    {
        $sale->loadMissing('items');
        if ($sale->items->isEmpty()) {
            throw new \RuntimeException('Sale belum memiliki item.');
        }

        $productIds = $sale->items->pluck('product_id')->all();

        $stocks = Stock::where('outlet_id', $sale->outlet_id)
            ->whereIn('product_id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('product_id');

        foreach ($sale->items as $item) {
            $stock = $stocks->get($item->product_id);
            $available = $stock ? $stock->qty : 0;

            if ($available < $item->qty) {
                throw new \RuntimeException('Stok tidak cukup untuk produk ID ' . $item->product_id);
            }
        }
    }

    private function finalizeSale(Sale $sale): void
    {
        $sale->status = Sale::STATUS_PAID;
        $sale->paid_at = now();
        $sale->save();
    }

    private function deductStockForSale(Sale $sale, ?int $userId = null): void
    {
        $sale->loadMissing('items');

        foreach ($sale->items as $item) {
            $stock = Stock::where('outlet_id', $sale->outlet_id)
                ->where('product_id', $item->product_id)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw new \RuntimeException('Stok tidak ditemukan untuk produk ID ' . $item->product_id);
            }

            $stock->qty = $stock->qty - $item->qty;
            $stock->save();

            $sale->inventoryMovements()->create([
                'outlet_id' => $sale->outlet_id,
                'product_id' => $item->product_id,
                'qty_delta' => -$item->qty,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'reason' => 'sale',
                'user_id' => $userId,
            ]);
        }
    }
}
