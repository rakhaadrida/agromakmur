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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->references('id')->on('sales_returns')->onDelete('cascade');
            $table->foreignId('sales_order_item_id')->nullable()->references('id')->on('sales_order_items')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('order_quantity')->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('actual_quantity')->default(0);
            $table->integer('delivered_quantity')->default(0);
            $table->integer('cut_bill_quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
