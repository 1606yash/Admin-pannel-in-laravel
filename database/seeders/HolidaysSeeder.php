<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('holidays')->insert([
            ['name' => 'New Year', 'date' => '2024-01-01'],
            ['name' => 'Republic Day', 'date' => '2024-01-26'],
            ['name' => 'Holi', 'date' => '2024-03-26'],
            ['name' => 'Gudi Padwa', 'date' => '2024-04-09'],
        ]);
    }
}
