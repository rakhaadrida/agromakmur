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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->enum('delivery_status', [
                \App\Utilities\Constant::SALES_ORDER_DELIVERY_STATUS_ACTIVE,
                \App\Utilities\Constant::SALES_ORDER_DELIVERY_STATUS_ON_PROGRESS,
                \App\Utilities\Constant::SALES_ORDER_DELIVERY_STATUS_COMPLETED,
            ])->default(\App\Utilities\Constant::SALES_ORDER_DELIVERY_STATUS_ACTIVE)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_status']);
        });
    }
};
