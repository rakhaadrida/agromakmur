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
        Schema::create('product_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_transfer_id')->references('id')->on('product_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('source_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreignId('destination_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('actual_quantity')->default(0);
            $table->foreignId('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transfer_items');
    }
};
