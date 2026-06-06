<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id('return_id');
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('restrict');
            $table->integer('quantity');
            $table->string('reason', 255);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'shipped', 'received', 'completed', 'rejected'])->default('pending');
            $table->date('return_date');
            $table->date('shipped_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('refund_status', 50)->nullable(); // pending, refunded, replaced, none
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_returns');
    }
};