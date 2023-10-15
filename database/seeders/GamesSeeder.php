<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dev1 = User::where('username', 'dev1')->firstOrFail();
        $dev2 = User::where('username', 'dev2')->firstOrFail();
        $games = [
            [
                'title' => 'Demo Game 1',
                'slug' => 'demo-game-1',
                'description' => 'This is demo game 1',
                'author_id' => $dev1->id
            ],
            [
                'title' => 'Demo Game 2',
                'slug' => 'demo-game-2',
                'description' => 'This is demo game 2',
                'author_id' => $dev2->id
            ],
        ];

        for ($i = 3; $i < 30; $i++) {
            $games[] = [
                'title' => 'Demo Game '.$i,
                'slug' => 'demo-game-'.$i,
                'description' => 'This is demo game '.$i,
                'author_id' => $dev1->id
            ];
        }

        DB::table('Games')->insert($games);
    }
}
