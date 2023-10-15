<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'username' => 'admin1',
                'password' => Hash::make('hellouniverse1!'),
            ],
            [
                'username' => 'admin2',
                'password' => Hash::make('hellouniverse2!'),
            ]
        ];

        DB::table('admins')->insert($admins);
    }
}
