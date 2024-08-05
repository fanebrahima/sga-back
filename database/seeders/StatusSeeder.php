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
            [
                'uuid' => "azerty03",
                'label' => "En attente des photos avant réparations",
                'value' => 3,
                'description' => "En attente des photos avant réparations",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
            [
                'uuid' => "azerty04",
                'label' => "En attente des photos pendant réparations",
                'value' => 4,
                'description' => "En attente des photos pendant réparations",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
            [
                'uuid' => "azerty05",
                'label' => "En attente des photos après réparations",
                'value' => 5,
                'description' => "En attente des photos après réparations",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
            [
                'uuid' => "azerty06",
                'label' => "Réparations terminées",
                'value' => 6,
                'description' => "Réparations terminées",
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
               
        ]);
    }
}
