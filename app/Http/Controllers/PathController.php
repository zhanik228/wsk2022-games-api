<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PathController extends Controller
{
    public function index(Game $game, $version, $path) {
        $path = Storage::disk('local')->path('/games/'.$game->id.'/'.$version.'/'.$path);
        return response()->file($path);
    }

    public function getHtml(Game $game, $version) {
        $path = Storage::disk('local')->path('/games/'.$game->id.'/'.$version.'/index.html');
        return response()->file($path);
    }
}
