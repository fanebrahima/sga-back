<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        \DB::table('users')->insert([

            [
                'first_name' => "ADMIN",
                'last_name' => "Admin",
                'email' => "admin@gmail.com",
                'phone' => "",
                'partner_id' => 1,
                'profil_id' => 1,
                'status_id' => 1,
                'password' => Hash::make("12345678"),
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,    
            ]
    
        ]);
    }
}
