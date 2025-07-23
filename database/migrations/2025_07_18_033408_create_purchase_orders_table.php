<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->string('number');
            $table->dateTime('date');
            $table->integer('tempo')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('tax_amount')->default(0);
            $table->double('grand_total')->default(0);
            $table->boolean('is_printed')->default(false);
            $table->enum('status', [
                \App\Utilities\Constant::PURCHASE_ORDER_STATUS_ACTIVE,
                \App\Utilities\Constant::PURCHASE_ORDER_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::PURCHASE_ORDER_STATUS_UPDATED,
                \App\Utilities\Constant::PURCHASE_ORDER_STATUS_CANCELLED
            ])->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
