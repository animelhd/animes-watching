<?php

namespace Animelhd\AnimesWatching;

use Illuminate\Database\Eloquent\Model;
use App\Models\Anime;
use Animelhd\AnimesWatching\Events\Watchinged;
use Animelhd\AnimesWatching\Events\Unwatchinged;

class Watching extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Watchinged::class,
        'deleted' => Unwatchinged::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('animeswatching.watchings_table');
        parent::__construct($attributes);
    }

    public function anime()
    {
        return $this->belongsTo(config('animeswatching.watchingable_model'), config('animeswatching.anime_foreign_key'));
    }

    public function user()
    {
        return $this->belongsTo(config('animeswatching.user_model'), config('animeswatching.user_foreign_key'));
    }
}
