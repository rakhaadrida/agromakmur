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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('number');
            $table->dateTime('date');
            $table->text('address');
            $table->boolean('is_printed')->default(false);
            $table->integer('print_count')->default(0);
            $table->enum('status', [
                \App\Utilities\Constant::DELIVERY_ORDER_STATUS_ACTIVE,
                \App\Utilities\Constant::DELIVERY_ORDER_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::DELIVERY_ORDER_STATUS_UPDATED,
                \App\Utilities\Constant::DELIVERY_ORDER_STATUS_CANCELLED
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
        Schema::dropIfExists('delivery_orders');
    }
};
