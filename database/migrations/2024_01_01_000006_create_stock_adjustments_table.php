<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id('adjustment_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('restrict');
            $table->enum('adjustment_type', ['return_in', 'damage_out', 'lost']);
            $table->integer('quantity');
            $table->string('reason', 255);
            $table->foreignId('reference_id')->nullable();
            $table->date('adjustment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};