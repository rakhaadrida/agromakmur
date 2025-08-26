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
        Schema::create('account_receivable_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_receivable_id')->references('id')->on('account_receivables')->onDelete('cascade');
            $table->foreignId('sales_return_id')->references('id')->on('sales_returns')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('actual_quantity')->default(0);
            $table->foreignId('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->double('price')->default(0);
            $table->double('total')->default(0);
            $table->string('discount')->nullable();
            $table->double('discount_amount')->default(0);
            $table->double('final_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivable_returns');
    }
};
