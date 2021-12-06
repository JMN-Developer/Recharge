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
    //     \App\Models\User::factory(10)->create();
    //    \App\Models\RechargeHistory::factory(10000)->create();
      \App\Models\ApiList::create([
           'type'=>'International',
           'company_name'=>'Reloadly',
       ]);
       \App\Models\ApiList::create([
        'type'=>'International',
        'company_name'=>'Ding Connect',
    ]);

    \App\Models\ApiList::create([
        'type'=>'Domestic',
        'company_name'=>'Epay',
    ]);

    \App\Models\ApiList::create([
        'type'=>'Domestic',
        'company_name'=>'Prepay',
    ]);
    }
}
