<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to stock_adjustments
        if (!Schema::hasColumn('stock_adjustments', 'reference_type')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->string('reference_type')->nullable()->after('reason');
            });
        }
        if (!Schema::hasColumn('stock_adjustments', 'reference_id')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            });
        }

        // Add to stock_movements
        if (!Schema::hasColumn('stock_movements', 'user_id')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('stock_after');
            });
        }
    }

    public function down(): void
    {
        //
    }
};