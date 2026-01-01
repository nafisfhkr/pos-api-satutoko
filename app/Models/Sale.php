<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PAID = 'PAID';

    protected $guarded = ['id'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'reference_id')
            ->where('reference_type', 'sale');
    }
}
