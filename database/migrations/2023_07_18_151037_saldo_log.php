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
        Schema::create('saldo_log', function (Blueprint $table) {
            $table->id();
            $table->integer('saldo_id');
            $table->integer('user_id');
            $table->text('type');
            $table->integer('saldo_before');
            $table->integer('saldo_trans');
            $table->integer('saldo_after');
            $table->integer('bonus_before');
            $table->integer('bonus_trans');
            $table->integer('bonus_after');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::drop('game_users');
    }
};
