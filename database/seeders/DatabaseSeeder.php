<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
       // \App\Models\RechargeHistory::factory(10000)->create();
       \App\Models\SecretStore::create([
        'type'=>'international',
        'company_name'=>'Dingconnect',
        'content'=>Crypt::encryptString('G4ymoFlN97B6PhZgK1yzuY'),
       ]);

       \App\Models\SecretStore::create([
        'type'=>'domestic',
        'company_name'=>'epay',
        'content'=>Crypt::encryptString('db2ec37cc93a3525'),
       ]);

    }
}
