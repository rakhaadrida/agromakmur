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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('contact_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->double('credit_limit')->default(0);
            $table->integer('tempo')->default(0);
            $table->foreignId('marketing_id')->nullable()->references('id')->on('marketings')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
};
