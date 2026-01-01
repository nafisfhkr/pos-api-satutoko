<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'cash_in' => 'decimal:2',
        'cash_out' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'variance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
