<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        \DB::table('profils')->insert([

            [
                'uuid' => "azerty01",
                'label' => "Administrateur",
                'value' => 1,
                'description' => "Administrateur",
                'status_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],
    
            [
                'uuid' => "azerty02",
                'label' => "Partenaire",
                'value' => 2,
                'description' => "Partenaire",
                'status_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],

            [
                'uuid' => "azerty03",
                'label' => "Expert",
                'value' => 3,
                'description' => "Expert",
                'status_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                //'created_by' => 1,
                //'updated_by' => 1,
            ],

    
        ]);
    }
}
