<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class game_list_slot extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        DB::beginTransaction();

        DB::statement("UPDATE game_list SET game_group_id = 'slot' WHERE id <= 10");

        DB::commit();
    }
}
