<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // client
        User::updateOrCreate(
            ['account' => 'marklee'],
            [
                'password'  => Hash::make('mark08'),
                'user_name' => 'mark',
                'role'      => 'client',
                'gender'    => 'M',
                'phone'     => '010-0000-0000',
                'birth'     => '1995-03-06',
            ]
        );

        // designer
        User::updateOrCreate(
            ['account' => 'haechannachana'],
            [
                'password'  => Hash::make('haechan0606'),
                'user_name' => 'haechan',
                'role'      => 'designer',
                'gender'    => 'M',
                'phone'     => '010-0000-0000',
                'birth'     => '2000-06-06',
            ]
        );

        // manager
        User::updateOrCreate(
            ['account' => 'jenojeno'],
            [
                'password'  => Hash::make('nojem'),
                'user_name' => 'jeno',
                'role'      => 'manager',
                'gender'    => 'M',
                'phone'     => '010-0000-0000',
                'birth'     => '2000-06-06',
            ]
        );
    }
}
