<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class OfferTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Offer::factory(10)->create();
        foreach (Offer::all() as $offer){
            $advertisements = Advertisement::inRandomOrder()->take(rand(1,3))->pluck('id');
            $offer->advertisements()->attach($advertisements);
        }
    }
}
