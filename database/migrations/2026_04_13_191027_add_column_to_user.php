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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();

            $table->integer('limit_project')->default(1);
            $table->integer('limit_bank')->default(2);

            $table->enum('withdraw_fee_type',['percent','fixed'])->default('fixed');
            $table->integer('withdraw_fee')->default(10000);
            $table->enum('fee_tx_type',['percent','fixed'])->default('fixed');
            $table->integer('fee_tx')->default(500);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
