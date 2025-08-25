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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreignId('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->string('number');
            $table->dateTime('date');
            $table->dateTime('delivery_date')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->integer('print_count')->default(0);
            $table->enum('status', [
                \App\Utilities\Constant::SALES_RETURN_STATUS_ACTIVE,
                \App\Utilities\Constant::SALES_RETURN_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::SALES_RETURN_STATUS_UPDATED,
                \App\Utilities\Constant::SALES_RETURN_STATUS_CANCELLED
            ])->nullable();
            $table->enum('delivery_status', [
                \App\Utilities\Constant::SALES_RETURN_DELIVERY_STATUS_ACTIVE,
                \App\Utilities\Constant::SALES_RETURN_DELIVERY_STATUS_ONGOING,
                \App\Utilities\Constant::SALES_RETURN_DELIVERY_STATUS_COMPLETED,
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
        Schema::dropIfExists('sales_returns');
    }
};
