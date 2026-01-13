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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->index('product_id', 'idx_product_prices_product_id');
            $table->index(['product_id', 'created_at'], 'idx_mainprice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropIndex('idx_product_prices_product_id');
            $table->dropIndex('idx_mainprice');
        });
    }
};
