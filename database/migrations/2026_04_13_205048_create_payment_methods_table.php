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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
               $table->string('group')->nullable();
            $table->string('method_code');
            $table->string('method_name');
            $table->string('gateway')->default('tripay');
            $table->string('image')->nullable();
            $table->string('description')->nullable();

            $table->boolean('is_qris')->default(false);
            $table->string('destination')->default('automatic');
            $table->string('destination_name')->default('automatic');

            $table->integer('min_amount')->nullable();
            $table->integer('max_amount')->nullable();

            
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->integer('fee_flat')->default(0);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
