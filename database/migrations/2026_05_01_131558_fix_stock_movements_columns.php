<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Change movement_type to VARCHAR
            if (Schema::hasColumn('stock_movements', 'movement_type')) {
                $table->string('movement_type', 50)->change();
            }
            
            // Change reference_type to VARCHAR
            if (Schema::hasColumn('stock_movements', 'reference_type')) {
                $table->string('reference_type', 50)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Revert if needed
        });
    }
};