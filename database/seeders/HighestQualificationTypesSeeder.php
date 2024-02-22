<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HighestQualificationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('highest_qualification_types')->insert(array (
            0 => 
            array (
                'type' => '10th Grade'            
            ),
            1 => 
            array (
                'type' => '12th Grade'
            ),
            2 => 
            array (
                'type' => 'Graduation'
            )
        ));
    }
}
