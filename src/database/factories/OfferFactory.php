<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start_date = $this->faker->dateTime();
        $end_date = clone $start_date;
        $end_date->modify('+1 day');
        return [
            'product_name' => $this->faker->company(),
            'discount_value' => $this->faker->numberBetween(10,90),
            'start_date' =>$start_date,
            'end_date' => $end_date
        ];
    }
}
