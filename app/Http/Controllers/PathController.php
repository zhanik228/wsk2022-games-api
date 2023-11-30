<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PathController extends Controller
{
    public function index(Game $game, $version) {
        $path = Storage::disk('local')->path('/games/'.$game->id.'/'.$version.'/'.'thumbnail.png');
        return response()->file($path);
    }

    public function getHtml(Game $game) {
        $path = Storage::disk('local')->path('/games/'.$game->id.'/'.$game->latestVersion->version.'/index.html');
        return response()->file($path);
    }
}
