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

        // \App\Models\currency_rate::create([
        //             'bdt'=>'1',
        //             'eur'=>'96.308462',

        //         ]);

        \App\Models\service_control::create([
            'service_name'=>'Bangladeshi Recharge',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'International Recharge',

        ]);


        \App\Models\service_control::create([
            'service_name'=>'Domestic Recharge',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'Pin',

        ]);


        \App\Models\service_control::create([
            'service_name'=>'White Calling',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'Sim',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'Cargo',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'Flight',

        ]);

        \App\Models\service_control::create([
            'service_name'=>'Support',

        ]);


        \App\Models\service_control::create([
            'service_name'=>'Transaction History',

        ]);

        // ]);

    //     \App\Models\service_code::create([
    //         'service_name'=>'International Recharge',
    //         'company_name'=>'Ding Connect',
    //         'service_code'=>10

    //     ]);

    //     \App\Models\service_code::create([
    //         'service_name'=>'International Recharge',
    //         'company_name'=>'DtOne',
    //         'service_code'=>11

    //     ]);

    //     \App\Models\service_code::create([
    //         'service_name'=>'International Recharge',
    //         'company_name'=>'Reloadly',
    //         'service_code'=>12

    //     ]);

    //     \App\Models\service_code::create([
    //         'service_name'=>'International Recharge',
    //         'company_name'=>'PPN',
    //         'service_code'=>13

    //     ]);

    //     \App\Models\service_code::create([
    //         'service_name'=>'Domestic Recharge',
    //         'company_name'=>'PPN',
    //         'service_code'=>13

    //     ]);


     }
}
