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
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->dateTime('subject_date');
            $table->enum('type', [
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_GOODS_RECEIPT,
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_PRODUCT_TRANSFER,
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_SALES_ORDER,
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_PURCHASE_RETURN,
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_SALES_RETURN,
                \App\Utilities\Constant::PRODUCT_STOCK_LOG_TYPE_MANUAL_EDIT,
            ])->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->integer('initial_stock')->default(0);
            $table->integer('quantity')->default(0);
            $table->double('final_amount')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_logs');
    }
};
