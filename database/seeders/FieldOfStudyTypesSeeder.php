<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FieldOfStudyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('field_of_study_types')->insert(array (
            0 => 
            array (
                'type' => 'Engineering'            
            ),
            1 => 
            array (
                'type' => 'Computer Science'
            ),
            2 => 
            array (
                'type' => 'Business'
            )
        ));
    }
}
