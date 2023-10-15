<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

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
            $authoredGames = $authoredGames->whereNull('game_versions.deleted_at');
        }

        return $authoredGames->get()->map(function($authoredGame) {
            return [
                'slug' => $authoredGame->slug,
                'title' => $authoredGame->title,
                'description' => $authoredGame->description
            ];
        });
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
