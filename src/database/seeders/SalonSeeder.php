<?php

namespace Database\Seeders;

use App\Models\Salon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Salon::create([
            'image'        => 'sample',
            'image_key'    => 'sample',
            'introduction' => 'A healing salon where you can forget about everyday life and truly relax.',
            'information'  => [
                "address"      => "seoul",
                "opening_hour" => "10:00 - 19:00",
                "holiday"      => "sunday",
                "phone" => "010-4819-7975",
                ],

            'map' => 'https://pub-08298820ca884cc49d536c1b0ce8b7c4.r2.dev/salon/1.png',

            'traffic' => [
                "bus"        => "706, 719, 730",
                "parking"    => "pass",
                "directions" => "pass",
                ]
        ]);
    }
}
