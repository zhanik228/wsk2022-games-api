<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GameVersion extends Model
{
    use HasFactory, SoftDeletes;

    public function getThumbnailPath() {
        return 'games/'.$this->game_id.'/'.$this->version.'/thumbnail.png';
    }

    public function hasThumbnail() {
        return Storage::disk('local')->exists($this->getThumbnailPath());
    }

}
