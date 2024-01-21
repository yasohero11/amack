<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::create([
            "name" => "Super Admin",
            "email" => "admin1@gmail.com",
            "password" => bcrypt("12345678")
        ]);

        $admin->assignRole("admin");


        $admin2 = Admin::create([
            "name" => "Super Admin",
            "email" => "admin2@gmail.com",
            "password" => bcrypt("12345678")
        ]);

        $admin2->assignRole("admin");
    }
}
