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
            $table->string('nav_login_2')->after('nav_login')->default('#000');
            $table->integer('nav_color_trans')->after('nav_color')->default(100);
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
