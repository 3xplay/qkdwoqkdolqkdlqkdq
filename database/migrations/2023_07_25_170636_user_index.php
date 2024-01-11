<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        
        DB::statement("DROP INDEX IF EXISTS saldo_user_id_index ON saldo");

        Schema::table('saldo', function (Blueprint $table) {
            $table->index(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {    
            $table->index(['name', 'telp', 'email']);
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
