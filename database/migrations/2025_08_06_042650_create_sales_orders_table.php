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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreignId('marketing_id')->references('id')->on('marketings')->onDelete('cascade');
            $table->string('number');
            $table->dateTime('date');
            $table->dateTime('delivery_date');
            $table->integer('tempo')->default(0);
            $table->boolean('is_taxable')->default(false);
            $table->double('subtotal')->default(0);
            $table->double('tax_amount')->default(0);
            $table->double('grand_total')->default(0);
            $table->double('payment_amount')->default(0);
            $table->double('outstanding_amount')->default(0);
            $table->boolean('is_printed')->default(false);
            $table->integer('print_count')->default(0);
            $table->enum('status', [
                \App\Utilities\Constant::SALES_ORDER_STATUS_ACTIVE,
                \App\Utilities\Constant::SALES_ORDER_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::SALES_ORDER_STATUS_UPDATED,
                \App\Utilities\Constant::SALES_ORDER_STATUS_CANCELLED
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
        Schema::dropIfExists('sales_orders');
    }
};
