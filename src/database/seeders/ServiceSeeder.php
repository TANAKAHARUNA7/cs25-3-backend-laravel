<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(
            ['service_name' => 'MEN CUT'],
            ['price' => 1200, 'duration_min' => 40]
        );

        Service::updateOrCreate(
            ['service_name' => 'WOMEN CUT'],
            ['price' => 3000, 'duration_min' => 60]
        );
    }
}
