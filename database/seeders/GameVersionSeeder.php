<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $game1 = Game::where('slug', 'demo-game-1')->firstOrFail();
        $game2 = Game::where('slug', 'demo-game-2')->firstOrFail();

        $gameVersions = [
            [
                'game_id' => $game1->id,
                'storage_path' => 'games/'.$game1->id.'/v1/',
                'version' => 'v1',
                'created_at' => now()->subMinutes(2),
                'deleted_at' => now(),
            ],
            [
                'game_id' => $game1->id,
                'storage_path' => 'games/'.$game1->id.'/v2/',
                'version' => 'v2',
                'created_at' => now()->subMinutes(2),
                'deleted_at' => null,
            ],
            [
                'game_id' => $game2->id,
                'storage_path' => 'games/'.$game2->id.'/v1/',
                'version' => 'v1',
                'created_at' => now()->subMinutes(1),
                'deleted_at' => null,
            ]
        ];

        for ($i = 3; $i < 28; $i++) {
            $gameVersions[] = [
                'game_id' => $i,
                'storage_path' => 'games/'.$i.'/v1/',
                'version' => 'v1',
                'created_at' => now()->subMinutes(1),
                'deleted_at' => null,
            ];
        }

        DB::table('game_versions')->insert($gameVersions);
    }
}
