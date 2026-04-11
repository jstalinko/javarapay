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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('merchant_ref');
            $table->boolean('is_production');
            $table->string('payment_method_code');
            $table->string('payment_method_name');
            $table->integer('amount');
            $table->integer('total_fee');
            $table->integer('total_amount');
            $table->text('notes')->nullable();
            $table->enum('status',['PAID','UNPAID','EXPIRED','REFUND','CANCELED'])->default('UNPAID');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
