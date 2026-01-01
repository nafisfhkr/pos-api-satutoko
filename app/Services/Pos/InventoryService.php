<?php

namespace App\Services\Pos;

use App\Models\InventoryMovement;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function adjustStock(int $outletId, int $productId, int $qtyDelta, ?string $reason, ?int $userId = null): Stock
    {
        return DB::transaction(function () use ($outletId, $productId, $qtyDelta, $reason, $userId) {
            $stock = Stock::where('outlet_id', $outletId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                $stock = Stock::create([
                    'outlet_id' => $outletId,
                    'product_id' => $productId,
                    'qty' => 0,
                ]);
            }

            $stock->qty = $stock->qty + $qtyDelta;
            $stock->save();

            $this->recordMovement($outletId, $productId, $qtyDelta, 'adjustment', null, $reason, $userId);

            return $stock;
        });
    }

    public function recordMovement(
        int $outletId,
        int $productId,
        int $qtyDelta,
        string $referenceType,
        ?int $referenceId,
        ?string $reason,
        ?int $userId = null
    ): InventoryMovement {
        return InventoryMovement::create([
            'outlet_id' => $outletId,
            'product_id' => $productId,
            'qty_delta' => $qtyDelta,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reason' => $reason,
            'user_id' => $userId,
        ]);
    }
}
