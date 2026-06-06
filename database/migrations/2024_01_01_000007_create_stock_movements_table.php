<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->enum('movement_type', ['sale', 'delivery', 'adjustment']);
            $table->integer('quantity');
            $table->enum('reference_type', ['sale', 'delivery', 'adjustment']);
            $table->unsignedBigInteger('reference_id');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};