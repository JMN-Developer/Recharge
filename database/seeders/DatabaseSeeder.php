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

        \App\Models\service_code::create([
            'service_name'=>'International Recharge',
            'company_name'=>'Ding Connect',
            'service_code'=>10

        ]);

        \App\Models\service_code::create([
            'service_name'=>'International Recharge',
            'company_name'=>'DtOne',
            'service_code'=>11

        ]);

        \App\Models\service_code::create([
            'service_name'=>'International Recharge',
            'company_name'=>'Reloadly',
            'service_code'=>12

        ]);

        \App\Models\service_code::create([
            'service_name'=>'International Recharge',
            'company_name'=>'PPN',
            'service_code'=>13

        ]);

        \App\Models\service_code::create([
            'service_name'=>'Domestic Recharge',
            'company_name'=>'PPN',
            'service_code'=>13

        ]);


    }
}
