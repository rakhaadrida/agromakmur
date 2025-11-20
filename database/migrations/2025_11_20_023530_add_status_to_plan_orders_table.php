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
        Schema::table('plan_orders', function (Blueprint $table) {
            $table->enum('status', [
                \App\Utilities\Constant::PLAN_ORDER_STATUS_ACTIVE,
                \App\Utilities\Constant::PLAN_ORDER_STATUS_UPDATED,
                \App\Utilities\Constant::PLAN_ORDER_STATUS_CANCELLED
            ])->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_orders', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};
