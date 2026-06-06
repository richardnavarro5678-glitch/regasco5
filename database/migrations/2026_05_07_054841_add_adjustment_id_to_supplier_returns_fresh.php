<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('supplier_returns', 'adjustment_id')) {
            Schema::table('supplier_returns', function (Blueprint $table) {
                $table->unsignedBigInteger('adjustment_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('supplier_returns', 'adjustment_id')) {
            Schema::table('supplier_returns', function (Blueprint $table) {
                $table->dropColumn('adjustment_id');
            });
        }
    }
};