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
        Schema::create('account_payable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_payable_id')->references('id')->on('account_payables')->onDelete('cascade');
            $table->dateTime('date');
            $table->double('amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payable_payments');
    }
};
