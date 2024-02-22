<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('bank_lists')->insert(array (
            0 => 
            array (
                'bank_name' => 'Central Bank of India'
            ),
            1 => 
            array (
                'bank_name' => 'State Bank of India'
            ),
            2 => 
            array (
                'bank_name' => 'HDFC'
            ),
            3 => 
            array (
                'bank_name' => 'Punjab National Bank'
            )
        ));
    }
}
