<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('districts')->insert(array (
            0 => 
            array (
                'district_name' => 'Indore',
                'state_id' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            )
        ));
    }
}
