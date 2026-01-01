<?php

namespace App\Services\Pos;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Str;

class SalesService
{
    public function createSale(int $outletId, ?int $userId, ?string $note = null): Sale
    {
        return Sale::create([
            'outlet_id' => $outletId,
            'user_id' => $userId,
            'status' => Sale::STATUS_DRAFT,
            'invoice_no' => $this->generateInvoiceNo(),
            'note' => $note,
        ]);
    }

    public function addItem(Sale $sale, int $productId, int $qty, ?float $unitPrice = null): Sale
    {
        if ($sale->status !== Sale::STATUS_DRAFT) {
            throw new \RuntimeException('Sale sudah tidak bisa diubah.');
        }

        $product = Product::findOrFail($productId);
        $price = $unitPrice ?? (float) $product->price;

        $item = SaleItem::where('sale_id', $sale->id)
            ->where('product_id', $productId)
            ->first();

        if (! $item) {
            $item = new SaleItem([
                'sale_id' => $sale->id,
                'product_id' => $productId,
            ]);
        }

        $item->qty = $qty;
        $item->unit_price = $price;
        $item->subtotal = $price * $qty;
        $item->save();

        $this->recalculateTotals($sale);

        return $sale->fresh(['items.product']);
    }

    public function recalculateTotals(Sale $sale): void
    {
        $subtotal = $sale->items()->sum('subtotal');
        $sale->subtotal = $subtotal;
        $sale->discount = $sale->discount ?? 0;
        $sale->tax = $sale->tax ?? 0;
        $sale->total = ($sale->subtotal - $sale->discount) + $sale->tax;
        $sale->save();
    }

    private function generateInvoiceNo(): string
    {
        return 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6));
    }
}
