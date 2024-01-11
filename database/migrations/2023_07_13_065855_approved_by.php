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
        //
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['approved_by']);
        });
        
        Schema::table('transaksi', function (Blueprint $table) {
            $table->integer('approved_by')->after('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
