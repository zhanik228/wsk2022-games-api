<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use HasFactory;

    protected $appends = ['author', 'thumbnail', 'uploadTimestamp', 'scoreCount'];

    // public function getScoreCountAttribute() {
    //     $scoreCount = Score::select(DB::raw('SUM(score) as score_count'))
    //         ->leftJoin(
    //             'game_versions',
    //             'game_versions.id',
    //             'scores.game_version_id'
    //         )
    //         ->where('game_versions.game_id', $this->game_id)
    //         ->groupBy('game_versions.game_id')
    //         ->get();

    //     return intval($scoreCount->pluck('score_count')->implode(''));
    // }

    public function getUploadTimestampAttribute() {
        if ($this->latestVersion) {
            return $this->latestVersion->created_at;
        }
        return null;
    }

    public function getGamePathAttribute() {
        if ($this->latestVersion) {
            return '/games/'.$this->slug.'/'.$this->latestVersion->version.'/';
        }
        return null;
    }

    public function getThumbnailAttribute() {
        if ($this->latestVersion) {
            $imageExists = Storage::disk('local')->exists('games/'.$this->game_id.'/'.$this->latestVersion->version.'/thumbnail.png');
            if ($imageExists) {
                return $this->getGamePathAttribute().'thumbnail.png';
            }
        }
        return null;
    }

    public function getAuthorAttribute() {
        if ($this->gameAuthor) {
            return $this->gameAuthor->username;
        }
        return null;
    }

    public function gameAuthor() {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function latestVersion() {
        return $this->hasOne(GameVersion::class)->whereNull('deleted_at');
    }

    public function thumbLatestVersion() {
        return $this->hasOne(GameVersion::class, 'game_id', 'game_id')->whereNull('deleted_at');
    }

    public function versions() {
        return $this->hasMany(GameVersion::class)->withTrashed();
    }
}
