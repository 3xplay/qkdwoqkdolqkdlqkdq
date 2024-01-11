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
        
        Schema::table('game_betting', function (Blueprint $table) {
            $table->dropColumn(['game_id']); 
        });
        
        Schema::table('game_betting', function (Blueprint $table) {
            $table->text('game_id')->after('pid')->nullable();
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
