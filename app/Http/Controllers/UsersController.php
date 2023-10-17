<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        $authoredGames = Game::where('author_id', $user->id)
            ->leftJoin('game_versions', 'game_versions.game_id', 'games.id');

        if ($request->user('sanctum')->id !== $user->id) {
            $authoredGames = $authoredGames->whereNotNull('game_versions.game_id');
        }

        $authoredGames = $authoredGames->get()->map(function($authoredGame) {
            return [
                'slug' => $authoredGame->slug,
                'title' => $authoredGame->title,
                'description' => $authoredGame->description
            ];
        });

        $highscores = Score::select(
            'Games.title',
            'Games.slug',
            'Games.description',
            'Games.id as game_id',
            DB::raw('MAX(scores.score) as highscore')
            )
            ->leftJoin('game_versions', 'game_versions.id', 'scores.game_version_id')
            ->leftJoin('Games', 'Games.id', 'game_versions.game_id')
            ->where('scores.user_id', $user->id)
            ->groupBy('game_versions.game_id')
            ->get()
            ->map(function($highscore) use ($user) {
                return [
                    'game' => [
                        'slug' => $highscore->slug,
                        'title' => $highscore->title,
                        'description' => $highscore->description
                    ],
                    'score' => $highscore->highscore,
                    'timestamp' => Score::
                    where('score', $highscore->highscore)
                    ->first()
                    ->created_at
                ];
            });

        return [
            'username' => $user->username,
            'registeredTimestamp' => $user->created_at,
            'authoredGames' => $authoredGames,
            'highscores' => $highscores
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
