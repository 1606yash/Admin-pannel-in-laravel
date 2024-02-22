<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('departments')->insert(array (
            0 => 
            array (
                'department_name' => 'Administration',
                'created_by' => '1',
                'updated_by' => '1'
            ),
            1 => 
            array (
                'department_name' => 'Ambulance',
                'created_by' => '1',
                'updated_by' => '1'
            )
        ));
    }
}
