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
        Schema::create('account_receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->enum('status', [
                \App\Utilities\Constant::ACCOUNT_RECEIVABLE_STATUS_UNPAID,
                \App\Utilities\Constant::ACCOUNT_RECEIVABLE_STATUS_ONGOING,
                \App\Utilities\Constant::ACCOUNT_RECEIVABLE_STATUS_PAID
            ])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivables');
    }
};
