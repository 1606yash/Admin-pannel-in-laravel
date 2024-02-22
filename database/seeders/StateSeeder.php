<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('states')->insert(array (
            0 => 
            array (
                'state_name' => 'Madhya Pradesh',
                'created_by' => '1',
                'updated_by' => '1'
            )
        ));
    }
}
