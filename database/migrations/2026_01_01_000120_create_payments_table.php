<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('method', 20);
            $table->decimal('amount', 15, 2);
            $table->decimal('cash_received', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();
            $table->string('reference_no')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
