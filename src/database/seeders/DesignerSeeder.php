<?php

namespace Database\Seeders;

use App\Models\Designer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DesignerSeeder extends Seeder
{
    public function run(): void
    {
        $designerUser = User::where("account", 'haechannachana')->firstOrFail();

        Designer::updateOrCreate(
            ['user_id' => $designerUser->id],
            [
                'image'      => 'dummy.jpg',
                'image_key'  => 'dummy.jpg',
                'experience' => 3,
                'good_at'    => 'Ladys Cut',
                'personality' => 'kind',
                'message' => ':)',
            ]
        );
    }
}
