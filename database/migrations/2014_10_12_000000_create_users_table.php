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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->enum('role', [
                \App\Utilities\Constant::USER_ROLE_SUPER_ADMIN,
                \App\Utilities\Constant::USER_ROLE_ADMIN,
                \App\Utilities\Constant::USER_ROLE_FINANCE,
                \App\Utilities\Constant::USER_ROLE_WAREHOUSE,
                \App\Utilities\Constant::USER_ROLE_SALES,
                \App\Utilities\Constant::USER_ROLE_SUPER_ADMIN_BRANCH
            ])->nullable();
            $table->enum('status', [
                \App\Utilities\Constant::USER_STATUS_PENDING,
                \App\Utilities\Constant::USER_STATUS_ACTIVE,
            ])->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->boolean('is_destroy')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
