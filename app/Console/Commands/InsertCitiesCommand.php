<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InsertCitiesCommand extends Command
{
    protected $signature = 'cities:insert';

    public function handle()
    {
        $jsonFile = Storage::path('public/network.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);

        $citiesData = $jsonData['cities'];

        foreach ($citiesData as $cityData) {
            $id = $cityData['id'];
            $name = $cityData['name'];

            \DB::table('bus_cities')->insert([
                'city_id' => $id,
                'name' => $name,
            ]);
        }

        $this->info('Cities inserted successfully!');
    }
}
