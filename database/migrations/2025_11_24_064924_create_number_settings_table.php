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
        Schema::create('number_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('key', [
                \App\Utilities\Constant::NUMBER_SETTING_KEY_PLAN_ORDER,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_GOODS_RECEIPT,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_PRODUCT_TRANSFER,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_SALES_ORDER,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_DELIVERY_ORDER,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_SALES_RETURN,
                \App\Utilities\Constant::NUMBER_SETTING_KEY_PURCHASE_RETURN,
            ]);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->integer('last_number')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_settings');
    }
};
