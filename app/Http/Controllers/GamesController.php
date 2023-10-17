<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use ZipArchive;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rules = [
            'page' => ['integer', 'min:0'],
            'size' => ['integer', 'min:1'],
            'sortBy' => ['in:title,popular,uploaddate'],
            'sortDir' => ['in:desc,asc']
        ];

        $request->validate($rules);

        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $games = Game::select('*')
        ->leftJoin('game_versions', function($join) {
            $join->on('game_versions.game_id', '=', 'games.id')
            ->whereNull('game_versions.deleted_at');
        })
        ->skip($page * $size)
        ->take($size)
        ->get()
        ->map(function($game) {
            return [
                'slug' => $game->slug,
                'title' => $game->title,
                'description' => $game->description,
                'thumbnail' => $game->thumbnail,
                'uploadTimestamp' => $game->uploadTimestamp,
                'author' => $game->author,
                'scoreCount' => $game->score_count
            ];
        });

        return [
            'page' => intval($page),
            'size' => $games->count(),
            'totalElements' => GameVersion::count(),
            'content' => $games
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'min:3', 'max:60'],
            'description' => ['required', 'min:0', 'max:200']
        ]);

        $generatedSlug = Str::slug($request->title);

        $slug = Game::where('slug', $generatedSlug)->first();

        if ($slug) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Game title already exists'
            ], 400);
        }

        $game = new Game();
        $game->author_id = auth('sanctum')->user()->id;
        $game->title = $request->title;
        $game->slug = $generatedSlug;
        $game->description = $request->description;
        $game->created_at = now();
        $game->updated_at = now();
        $game->save();

        return response()->json([
            'status' => 'success',
            'slug' => $generatedSlug
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        return collect([
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => $game->thumbnail,
            'uploadTimestamp' => $game->upload_timestamp,
            'author' => $game->author,
            'scoreCount' => $game->score_count,
            'gamePath' => $game->game_path
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        if ($game->author_id !== $request->user('sanctum')->id) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        $game->title = $request->title ?? $game->title;
        $game->description = $request->description ?? $game->description;
        $game->save();

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Game $game)
    {
        if ($request->user('sanctum')->id !== $game->author_id) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        foreach($game->gameVersions as $gameVersion) {
            foreach(
                Score::where(
                    'game_version_id',
                    $gameVersion->id
                )->get() as $gameScore
            )
            {
                $gameScore->forceDelete();
            }
            $gameVersion->forceDelete();
        }
        $game->delete();

        return response('', 204);
    }

    public function upload(Request $request, Game $game) {
        $request->validate([
            'zipfile' => ['required', 'file'],
            'token' => ['required']
        ]);

        $token = PersonalAccessToken::findToken($request->token);
        if (!$token) {
            return response('Unauthorized', 401);
        }

        $user = $token->tokenable;
        if (!$user || $user->id !== $game->author_id) {
            return response('You are not the author of the game', 403);
        }

        $nextVersion = 'v'.intval(substr($game->latestVersion->version, 1)) + 1;
        $basePath = Storage::disk('local')
        ->path('games/'.$game->id.'/'.$nextVersion);
        $absolutePath = str_replace('\\', '/', $basePath);

        if (!Storage::disk('local')->exists($absolutePath)) {
            try {
                Storage::disk('local')->makeDirectory($absolutePath);
            } catch (\Exception $e) {
                return response('cannot make a directory '.$e);
            }
        }

        $zipFile = new ZipArchive();

        if ($zipFile->open($request->zipfile) !== TRUE) {
            return response()->json('cannot open archive', 501);
        }

        if ($zipFile->extractTo($absolutePath.'/') !== TRUE) {
            return response()->json('cannot extract file', 501);
        };

        foreach ($game->gameVersions as $gameVersion) {
            $gameVersion->delete();
        }

        $storagePath = substr(
            $absolutePath,
            strpos($absolutePath, 'games')
        ).'/';
        $newGameVersion = new GameVersion();
        $newGameVersion->game_id = $game->id;
        $newGameVersion->version = $nextVersion;
        $newGameVersion->storage_path = $storagePath;
        $newGameVersion->created_at = now();
        $newGameVersion->save();

        return response()->json([
            'status' => 'success',
            'message' => 'version uploaded successfully'
        ], 200);
    }
}
