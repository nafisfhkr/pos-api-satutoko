<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('cash_in', 15, 2)->default(0);
            $table->decimal('cash_out', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->decimal('expected_cash', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
