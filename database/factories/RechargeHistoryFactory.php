<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RechargeHistory;


class RechargeHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RechargeHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'reseller_id'=>14,
            'number'=>$this->faker->phoneNumber,
            'amount'=>$this->faker->numberBetween(0, 20),
            'txid'=>$this->faker->numberBetween(1000000,999999999),
            'type'=>'International',
            'status'=>'completed',


        ];
    }
}
