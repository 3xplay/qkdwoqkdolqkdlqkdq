<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class game_unlock extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        DB::beginTransaction();

        DB::statement("UPDATE game_list SET game_locked = 0 WHERE id > 0");

        DB::commit();
    }
}
