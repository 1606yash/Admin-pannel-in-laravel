<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $id = DB::table("users")->insertGetId([
            "first_name" => 'Super',
            "last_name" => 'Admin',
            "email" => 'parivaar@yopmail.com',
            "password" => '$2y$10$araoIIusPBlfekiI2q4OxebYXGVe23HByXRreofLZKDvPHFYDNICO',
            "phone_no" => '1234567890',
            "address" => 'Skye Indore, Madhya Pradesh',
            "state_id" => 1,
            "district_id" => 1,
            "role_id" => 1,
            "is_active" => 1,
            "is_verified" => 1,
            "address" => 'Skye Indore, Madhya Pradesh',
            "created_by" => 1,
            "updated_by" => 1,
        ]);
    }
}
