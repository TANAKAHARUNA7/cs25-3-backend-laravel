<?php

namespace Database\Seeders;

use App\Models\Designer;
use App\Models\TimeOff;
use Illuminate\Database\Seeder;

class TimeOffSeeder extends Seeder
{
    public function run(): void
    {
        $designer = Designer::firstOrFail();

        TimeOff::create([
            'designer_id' => $designer->id,
            'start_at'    => '2025-02-20',
            'end_at'      => '2025-02-21',
        ]);        
    }
}
