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
        Schema::table('approval_items', function (Blueprint $table) {
            $table->double('wages')->nullable()->after('price');
            $table->double('shipping_cost')->nullable()->after('wages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_items', function (Blueprint $table) {
            $table->dropColumn(['wages', 'shipping_cost']);
        });
    }
};
