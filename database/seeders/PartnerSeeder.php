<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        \DB::table('partners')->insert([

            [
                'uuid' => "azerty01",
                'name' => "GERENTHON & CIE",
                'status_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
    
        ]);
    }
}
