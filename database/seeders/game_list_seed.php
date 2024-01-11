<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class game_list_seed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        DB::beginTransaction();

        $sql = file_get_contents(__DIR__.'/sql/game_list.sql');
        DB::insert($sql);

        $sql = file_get_contents(__DIR__.'/sql/game_setting.sql');
        DB::insert($sql);

        $sql = file_get_contents(__DIR__.'/sql/game_api.sql');
        DB::insert($sql);
        
        DB::commit();
    }
}
