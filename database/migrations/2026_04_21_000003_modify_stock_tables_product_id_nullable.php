<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock Movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
        });

        // Stock Adjustments
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('restrict');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('restrict');
        });
    }
};