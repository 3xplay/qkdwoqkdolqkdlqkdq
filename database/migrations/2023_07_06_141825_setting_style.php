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
        Schema::table('profil', function (Blueprint $table) {
            $table->string('nav_color_2')->after('nav_color')->default('#000');
            $table->string('bg_color_2')->after('bg_color')->default('#000');
            $table->string('bg_color_single')->after('bg_color_2')->default(0);
            $table->string('txt_color')->after('nav_color_2')->default('#000');
            $table->string('nav_login')->after('bg_color_2')->default('#000');
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
