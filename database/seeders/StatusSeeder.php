<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        \DB::table('statuses')->insert([

            [
                'uuid' => "azerty01",
                'label' => "Actif(ve)",
                'value' => 1,
                'description' => "Actif(ve)",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
            [
                'uuid' => "azerty02",
                'label' => "Inactif(ve)",
                'value' => 2,
                'description' => "Inactif(ve)",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
               
        ]);
    }
}
