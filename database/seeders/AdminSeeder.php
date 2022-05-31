<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => 'admin@admin.com',
            'phone' => '0777007777',
            'password' => Hash::make('password'),
            'role' =>'manager',
        ]);
    }
}
