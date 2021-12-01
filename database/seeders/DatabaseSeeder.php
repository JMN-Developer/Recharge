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
       \App\Models\SecretStore::create([
           'type'=>'international',
           'company_name'=>'Reloadly',
           'content'=>Crypt::encrypt('SHILVCMRGJab2DfLIxhaKqCNxlgoLFvv'),
           'secret_type'=>'client_id',

       ]);
       \App\Models\SecretStore::create([
        'type'=>'international',
        'company_name'=>'Reloadly',
        'content'=>Crypt::encrypt('lQABjKdyr6-DgqqJXW5EVeDs1HoSrP-pvqH1WkEWMeMdnt4nYv9iZl8QCa2KGbz'),
        'secret_type'=>'client_secret',

    ]);

    }
}
