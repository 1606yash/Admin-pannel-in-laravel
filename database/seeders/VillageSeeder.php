<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('villages')->insert(array (
            0 => 
            array (
                'name' => 'Khatri Khedi',
                'district_id' => '1'
            )
        ));
    }
}
