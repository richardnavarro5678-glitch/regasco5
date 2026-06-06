<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Tanggalin yung contact_info column
            $table->dropColumn('contact_info');
            
            // Dagdagan ng phone at email
            $table->string('phone', 50)->nullable()->after('contact_person');
            $table->string('email', 255)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact_info')->nullable()->after('contact_person');
            $table->dropColumn(['phone', 'email']);
        });
    }
};