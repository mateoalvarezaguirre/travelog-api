<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use League\ISO3166\ISO3166;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = countries();

        $iso3166 = new ISO3166();

        foreach ($iso3166->all() as $country) {
            DB::table('countries')->insert([
                'id'           => $country['numeric'],
                'country_name' => $country['name'],
                'alpha2_code'  => $country['alpha2'],
                'alpha3_code'  => $country['alpha3'],
                'phone_prefix' => $countries[strtolower($country['alpha2'])]['calling_code'] ?? null,
            ]);
        }
    }
}
