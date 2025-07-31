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
        Schema::create('product_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->dateTime('date');
            $table->boolean('is_printed')->default(false);
            $table->enum('status', [
                \App\Utilities\Constant::PRODUCT_TRANSFER_STATUS_ACTIVE,
                \App\Utilities\Constant::PRODUCT_TRANSFER_STATUS_WAITING_APPROVAL,
                \App\Utilities\Constant::PRODUCT_TRANSFER_STATUS_CANCELLED
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
        Schema::dropIfExists('product_transfers');
    }
};
