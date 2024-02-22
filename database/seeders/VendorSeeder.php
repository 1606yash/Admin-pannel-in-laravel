<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('vendors')->insert(array (
            0 => 
            array (
                'name' => 'EmergencyFuel Solutions',
            ),
            1 => 
            array (
                'name' => 'RescueRefuel Specialists',
            ),
            2 => 
            array (
                'name' => 'FirstGear Ambulance Parts',
            ),
            3 => 
            array (
                'name' => 'MediCare Auto Accessories',
            )
        ));
    }
}
