<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add reference_type to stock_adjustments if not exists
        if (!Schema::hasColumn('stock_adjustments', 'reference_type')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->string('reference_type')->nullable()->after('reason');
            });
        }

        // Add user_id to stock_movements if not exists
        if (!Schema::hasColumn('stock_movements', 'user_id')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('stock_after');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_adjustments', 'reference_type')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->dropColumn('reference_type');
            });
        }

        if (Schema::hasColumn('stock_movements', 'user_id')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};