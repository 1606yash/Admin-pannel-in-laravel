<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('expense_types')->insert(array (
            0 => 
            array (
                'name' => 'Fuel',
                'description' => 'Fuel expenses for the ambulance'
            ),
            1 => 
            array (
                'name' => 'Maintenance',
                'description' => 'Maintenance expenses for the ambulance'
            ),
            2 => 
            array (
                'name' => 'Insurance',
                'description' => 'Insurance expenses for the ambulance'
            )
        ));
    }
}
