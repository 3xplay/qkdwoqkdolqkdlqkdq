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
        Schema::create('game_list', function (Blueprint $table) {
            $table->id();
            $table->text('provider')->nullable();
            $table->text('image')->nullable();
            $table->text('image_is_url')->nullable();
            
            $table->text('game_group_id')->nullable();
            $table->longText('game_id')->nullable();
            $table->longText('game_id_long')->nullable();
            $table->text('game_name')->nullable();
            $table->text('game_type_id')->nullable();
            $table->text('game_demo')->nullable();

            $table->text('category')->nullable();
            $table->text('technology')->nullable();
            $table->text('technology_id')->nullable();
            $table->text('platform')->nullable();
            $table->text('aspect_ratio')->nullable();
            $table->text('jurisdictions')->nullable();
            $table->text('frb_available')->nullable();
            $table->text('data_type')->nullable();
            $table->text('features')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('game_setting', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('price')->nullable();
            $table->text('diskon')->nullable();
            $table->integer('min_bet')->nullable();
            $table->integer('max_bet')->nullable();
            $table->integer('satuan')->nullable();
            $table->integer('parent')->nullable();

            $table->integer('status')->default(1);
            $table->timestamps();
        });
        
        Schema::create('game_betting', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('pid')->nullable();
            $table->integer('game_id')->nullable();
            $table->text('code')->nullable();
            $table->integer('periode')->nullable();
            $table->text('tebak')->nullable();
            $table->text('posisi')->nullable();
            $table->text('nominal')->nullable();
            $table->text('menang')->nullable();
            $table->text('diskon')->nullable();
            $table->text('bayar')->nullable();
            $table->text('jumlah')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->integer('status')->default(1);

            $table->timestamps();
        });

        Schema::create('game_stat', function (Blueprint $table) {
            $table->id();
            $table->text('ip')->nullable();
            $table->dateTime('dates')->nullable();
            $table->integer('hits')->nullable();
            $table->text('page')->nullable();
            $table->integer('user_id')->nullable();
            
            $table->timestamps();
        });

        Schema::create('game_api', function (Blueprint $table) {
            $table->id();
            $table->text('provider')->nullable();
            $table->text('provider_name')->nullable();
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->text('url_request')->nullable();
            $table->text('url_response')->nullable();
            
            $table->integer('status')->default(1);
            
            $table->timestamps();
        });

        
        Schema::create('game_transaction', function (Blueprint $table) {
            $table->id();
            $table->text('codes')->nullable();
            $table->dateTime('date')->nullable();
            $table->text('transaksi')->nullable();
            $table->integer('total')->nullable();
            $table->integer('saldo')->nullable();
            $table->text('notes')->nullable();
            $table->text('game_id')->nullable();
            $table->integer('provider_id')->nullable();
            $table->text('jenis')->nullable();
            $table->text('metode')->nullable();
            $table->integer('pay_from')->nullable();
            $table->integer('user_id')->nullable();
            
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
        Schema::drop('game_list');
        Schema::drop('game_setting');
        Schema::drop('game_betting');
        Schema::drop('game_stat');
        Schema::drop('game_api');
        Schema::drop('game_transaction');
    }
};
