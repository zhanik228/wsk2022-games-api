<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function index(Game $game) {
        $scores = Score::select(
            'scores.id as score_id',
            'Users.username',
            DB::raw('MAX(scores.score) AS score')
        )
        ->leftJoin(
            'game_versions',
            'game_versions.game_id',
            'scores.game_version_id'
        )
        ->leftJoin(
            'Users',
            'Users.id',
            'scores.user_id'
        )
        ->where('game_versions.game_id', $game->id)
        ->groupBy('Users.id')
        ->orderBy('scores.id', 'desc')
        ->get();

        return $scores;
    }

    public function store(Game $game, Request $request) {
        $request->validate([
            'score' => 'required'
        ]);

        $gameVersion = GameVersion::where('game_id', $game->id)->first();

        $newScore = new Score();
        $newScore->user_id = $request->user('sanctum')->id;
        $newScore->game_version_id = $gameVersion->id;
        $newScore->score = $request->score;
        $newScore->created_at = now();
        $newScore->updated_at = now();
        $newScore->save();

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
