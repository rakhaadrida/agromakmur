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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreignId('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->string('number');
            $table->dateTime('date');
            $table->dateTime('received_date')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->integer('print_count')->default(0);
            $table->enum('status', [
                \App\Utilities\Constant::PURCHASE_RETURN_STATUS_ACTIVE,
                \App\Utilities\Constant::PURCHASE_RETURN_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::PURCHASE_RETURN_STATUS_UPDATED,
                \App\Utilities\Constant::PURCHASE_RETURN_STATUS_CANCELLED
            ])->nullable();
            $table->enum('receipt_status', [
                \App\Utilities\Constant::PURCHASE_RETURN_RECEIPT_STATUS_ACTIVE,
                \App\Utilities\Constant::PURCHASE_RETURN_RECEIPT_STATUS_ONGOING,
                \App\Utilities\Constant::PURCHASE_RETURN_RECEIPT_STATUS_COMPLETED
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
        Schema::dropIfExists('purchase_returns');
    }
};
