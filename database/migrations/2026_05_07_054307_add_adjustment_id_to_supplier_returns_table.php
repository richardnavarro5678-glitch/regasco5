<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_returns', function (Blueprint $table) {
            // Add as unsigned big integer lang muna (walang foreign key constraint)
            $table->unsignedBigInteger('adjustment_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_returns', function (Blueprint $table) {
            $table->dropColumn('adjustment_id');
        });
    }
};