<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountySeeder extends Seeder
{
    public function run()
    {
        $counties = [
            ['county_code' => 1, 'county_name' => 'Mombasa'],
            ['county_code' => 2, 'county_name' => 'Kwale'],
            ['county_code' => 3, 'county_name' => 'Kilifi'],
            ['county_code' => 4, 'county_name' => 'Tana River'],
            ['county_code' => 5, 'county_name' => 'Lamu'],
            ['county_code' => 6, 'county_name' => 'Taita-Taveta'],
            ['county_code' => 7, 'county_name' => 'Garissa'],
            ['county_code' => 8, 'county_name' => 'Wajir'],
            ['county_code' => 9, 'county_name' => 'Mandera'],
            ['county_code' => 10, 'county_name' => 'Marsabit'],
            ['county_code' => 11, 'county_name' => 'Isiolo'],
            ['county_code' => 12, 'county_name' => 'Meru'],
            ['county_code' => 13, 'county_name' => 'Tharaka-Nithi'],
            ['county_code' => 14, 'county_name' => 'Embu'],
            ['county_code' => 15, 'county_name' => 'Kitui'],
            ['county_code' => 16, 'county_name' => 'Machakos'],
            ['county_code' => 17, 'county_name' => 'Makueni'],
            ['county_code' => 18, 'county_name' => 'Nyandarua'],
            ['county_code' => 19, 'county_name' => 'Nyeri'],
            ['county_code' => 20, 'county_name' => 'Kirinyaga'],
            ['county_code' => 21, 'county_name' => 'Murang\'a'],
            ['county_code' => 22, 'county_name' => 'Kiambu'],
            ['county_code' => 23, 'county_name' => 'Turkana'],
            ['county_code' => 24, 'county_name' => 'West Pokot'],
            ['county_code' => 25, 'county_name' => 'Samburu'],
            ['county_code' => 26, 'county_name' => 'Trans-Nzoia'],
            ['county_code' => 27, 'county_name' => 'Uasin Gishu'],
            ['county_code' => 28, 'county_name' => 'Elgeyo-Marakwet'],
            ['county_code' => 29, 'county_name' => 'Nandi'],
            ['county_code' => 30, 'county_name' => 'Baringo'],
            ['county_code' => 31, 'county_name' => 'Laikipia'],
            ['county_code' => 32, 'county_name' => 'Nakuru'],
            ['county_code' => 33, 'county_name' => 'Narok'],
            ['county_code' => 34, 'county_name' => 'Kajiado'],
            ['county_code' => 35, 'county_name' => 'Kericho'],
            ['county_code' => 36, 'county_name' => 'Bomet'],
            ['county_code' => 37, 'county_name' => 'Kakamega'],
            ['county_code' => 38, 'county_name' => 'Vihiga'],
            ['county_code' => 39, 'county_name' => 'Bungoma'],
            ['county_code' => 40, 'county_name' => 'Busia'],
            ['county_code' => 41, 'county_name' => 'Siaya'],
            ['county_code' => 42, 'county_name' => 'Kisumu'],
            ['county_code' => 43, 'county_name' => 'Homa Bay'],
            ['county_code' => 44, 'county_name' => 'Migori'],
            ['county_code' => 45, 'county_name' => 'Kisii'],
            ['county_code' => 46, 'county_name' => 'Nyamira'],
            ['county_code' => 47, 'county_name' => 'Nairobi'],
        ];

        $now = now();
        foreach ($counties as &$county) {
            $county['created_at'] = $now;
            $county['updated_at'] = $now;
        }

        DB::table('county')->insert($counties);
    }
}
