<?php

namespace Animelhd\AnimesWatching\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Animelhd\AnimesWatching\Watching;

trait Watchingable
{
    public function watchings(): HasMany
    {
        return $this->hasMany(config('animeswatching.watching_model'), config('animeswatching.anime_foreign_key'));
    }
}
