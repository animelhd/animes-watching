<?php

namespace Animelhd\AnimesWatching\Traits;

use Animelhd\AnimesWatching\Watching;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Anime;

trait Watchinger
{
	public function watching(Anime $anime): void
    {
        if (! $this->hasWatchinged($anime)) {
            $this->watchings()->create([
                'anime_id' => $anime->getKey(),
            ]);
        }
    }

    public function unwatching(Anime $anime): void
    {
        $this->watchings()
            ->where('anime_id', $anime->getKey())
            ->delete();
    }

    public function toggleWatching(Anime $anime): void
    {
        $this->hasWatchinged($anime)
            ? $this->unwatching($anime)
            : $this->watching($anime);
    }

    public function hasWatchinged(Anime $anime): bool
    {
        return $this->watchings()
            ->where('anime_id', $anime->getKey())
            ->exists();
    }

    public function watchings()
    {
        return $this->hasMany(Watching::class, config('animeswatching.user_foreign_key'));
    }
}
