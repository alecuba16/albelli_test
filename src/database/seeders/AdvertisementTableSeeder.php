<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Advertisement::factory(10)->create();
        foreach (Advertisement::all() as $advertisement){
            $offers = Offer::inRandomOrder()->take(rand(1,3))->pluck('id');
            $advertisement->offers()->attach($offers);
        }
    }
}
