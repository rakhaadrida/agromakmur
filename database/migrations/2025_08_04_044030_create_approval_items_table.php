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
        Schema::create('approval_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->references('id')->on('approvals')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('actual_quantity')->default(0);
            $table->double('price')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_amount')->nullable();
            $table->double('total')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_items');
    }
};
