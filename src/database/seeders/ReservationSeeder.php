<?php

namespace Database\Seeders;

use App\Models\Designer;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::where('account', 'marklee')->firstOrFail();
        $designer = Designer::firstOrFail();

        $reservation = Reservation::create([
            'client_id'   => $client->id,
            'designer_id' => $designer->id,
            'requirement' => 'test!',
            'day'         => '2025-02-19',
            'start_at'    => '13:00:00',
            'end_at'      => '15:30:00',
            'status'      => 'pending',
        ]);

        $service = Service::where('service_name', 'MEN CUT')->firstOrFail();
        
        $reservation->services()->attach($service->id, [
            'qty' => 1,
            'unit_price' => $service->price,
        ]);

    }
}
