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
            $table->enum('type', [
                \App\Utilities\Constant::SALES_ORDER_TYPE_RETAIL,
                \App\Utilities\Constant::SALES_ORDER_TYPE_WHOLESALE,
            ])->nullable()->after('is_taxable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
};
