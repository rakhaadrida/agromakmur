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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->dateTime('date');
            $table->enum('type', [
                \App\Utilities\Constant::APPROVAL_TYPE_EDIT,
                \App\Utilities\Constant::APPROVAL_TYPE_CANCEL,
                \App\Utilities\Constant::APPROVAL_TYPE_APPROVAL_LIMIT
            ])->nullable();
            $table->enum('status', [
                \App\Utilities\Constant::APPROVAL_STATUS_PENDING,
                \App\Utilities\Constant::APPROVAL_STATUS_APPROVED,
                \App\Utilities\Constant::APPROVAL_STATUS_REJECTED
            ])->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('approvals');
    }
};
