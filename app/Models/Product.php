<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}