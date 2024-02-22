<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shift_types')->insert([
            ['shift_name' => 'Morning Shift'],
            ['shift_name' => 'Night Shift']
        ]);
    }
}
