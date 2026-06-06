<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tanggalin yung foreign key constraint muna
            $table->dropForeign(['product_id']);
            
            // Gawing nullable yung product_id
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            // Dagdag ulit yung foreign key pero with set null on delete
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('restrict');
        });
    }
};