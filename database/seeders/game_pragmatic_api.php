<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class game_pragmatic_api extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        DB::beginTransaction();

        DB::statement("UPDATE game_api SET api_key = 'rljp138_royaljp', url_request = 'https://api.prerelease-env.biz/IntegrationService/v3/http/CasinoGameAPI' WHERE provider = 'PragmaticPlay'");

        DB::commit();
    }
}
