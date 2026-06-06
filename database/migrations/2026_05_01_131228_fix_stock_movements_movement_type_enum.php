<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Change ENUM to VARCHAR para mas flexible
            $table->string('movement_type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Revert to ENUM if needed (optional)
            $table->enum('movement_type', ['delivery', 'sale', 'adjustment'])->change();
        });
    }
};