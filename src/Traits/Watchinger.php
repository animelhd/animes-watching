<?php

namespace Animelhd\AnimesWatching\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property \Illuminate\Database\Eloquent\Collection $watchings
 */
trait Watchinger
{
    public function watching(Model $object)
    {
        /* @var \Animelhd\AnimesWatching\Traits\Watchingable $object */
        if (!$this->hasWatchinged($object)) {
            $watching = app(config('animeswatching.watching_model'));
            $watching->{config('animeswatching.user_foreign_key')} = $this->getKey();

            $object->watchings()->save($watching);
        }
    }

    public function unwatching(Model $object)
    {
        /* @var \Animelhd\AnimesWatching\Traits\Watchingable $object */
        $relation = $object->watchings()
            ->where('watchingable_id', $object->getKey())
            ->where('watchingable_type', $object->getMorphClass())
            ->where(config('animeswatching.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    public function toggleWatching(Model $object)
    {
        $this->hasWatchinged($object) ? $this->unwatching($object) : $this->watching($object);
    }

    public function hasWatchinged(Model $object): bool
    {
        return ($this->relationLoaded('watchings') ? $this->watchings : $this->watchings())
            ->where('watchingable_id', $object->getKey())
            ->where('watchingable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function watchings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(config('animeswatching.watching_model'), config('animeswatching.user_foreign_key'), $this->getKeyName());
    }

    public function attachWatchingStatus($watchingables, callable $resolver = null)
    {
        $returnFirst = false;
        $toArray = false;

        switch (true) {
            case $watchingables instanceof Model:
                $returnFirst = true;
                $watchingables = \collect([$watchingables]);
                break;
            case $watchingables instanceof LengthAwarePaginator:
                $watchingables = $watchingables->getCollection();
                break;
            case $watchingables instanceof Paginator:
                $watchingables = \collect($watchingables->items());
                break;
            case \is_array($watchingables):
                $watchingables = \collect($watchingables);
                $toArray = true;
                break;
        }

        \abort_if(!($watchingables instanceof Collection), 422, 'Invalid $watchingables type.');

        $watchinged = $this->watchings()->get()->keyBy(function ($item) {
            return \sprintf('%s-%s', $item->watchingable_type, $item->watchingable_id);
        });

        $watchingables->map(function ($watchingable) use ($watchinged, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $watchingable = $resolver($watchingable);

            if ($watchingable && \in_array(Watchingable::class, \class_uses($watchingable))) {
                $key = \sprintf('%s-%s', $watchingable->getMorphClass(), $watchingable->getKey());
                $watchingable->setAttribute('has_watchinged', $watchinged->has($key));
            }
        });

        return $returnFirst ? $watchingables->first() : ($toArray ? $watchingables->all() : $watchingables);
    }

    /**
     * Get Query Builder for watchings
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function getWatchingItems(string $model)
    {
        return app($model)->whereHas(
            'watchingers',
            function ($q) {
                return $q->where(config('animeswatching.user_foreign_key'), $this->getKey());
            }
        );
    }
}
