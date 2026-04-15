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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('txid');
            $table->string('pay_url')->nullable()->after('reference');
            $table->string('pay_code')->nullable()->after('pay_url');
            $table->string('qr_url')->nullable()->after('pay_code');
            $table->timestamp('expired_at')->nullable()->after('qr_url');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
