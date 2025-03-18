## Laravel Watching

❤️ User watching feature for Laravel Application.

[![CI](https://github.com/overtrue/laravel-watching/workflows/CI/badge.svg)](https://github.com/overtrue/laravel-watching/actions)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-watching/v/stable.svg)](https://packagist.org/packages/overtrue/laravel-watching)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/laravel-watching/v/unstable.svg)](https://packagist.org/packages/overtrue/laravel-watching)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-watching/downloads)](https://packagist.org/packages/overtrue/laravel-watching)
[![License](https://poser.pugx.org/overtrue/laravel-watching/license)](https://packagist.org/packages/overtrue/laravel-watching)

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

```shell
composer require animelhd/animes-watching -vvv
```

### Configuration & Migrations

```php
php artisan vendor:publish --provider="Animelhd\AnimesWatching\WatchingServiceProvider"
```

## Usage

### Traits

#### `Animelhd\AnimesWatching\Traits\Watchinger`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Animelhd\AnimesWatching\Traits\Watchinger;

class User extends Authenticatable
{
    use Watchinger;

    <...>
}
```

#### `Animelhd\AnimesWatching\Traits\Watchingable`

```php
use Illuminate\Database\Eloquent\Model;
use Animelhd\AnimesWatching\Traits\Watchingable;

class Post extends Model
{
    use Watchingable;

    <...>
}
```

### API

```php
$user = User::find(1);
$post = Post::find(2);

$user->watching($post);
$user->unwatching($post);
$user->toggleWatching($post);
$user->getWatchingItems(Post::class)

$user->hasWatchinged($post);
$post->hasBeenWatchingedBy($user);
```

#### Get object watchingers:

```php
foreach($post->watchingers as $user) {
    // echo $user->name;
}
```

#### Get Watching Model from User.

Used Watchinger Trait Model can easy to get Watchingable Models to do what you want.
_note: this method will return a `Illuminate\Database\Eloquent\Builder` _

```php
$user->getWatchingItems(Post::class);

// Do more
$watchingPosts = $user->getWatchingItems(Post::class)->get();
$watchingPosts = $user->getWatchingItems(Post::class)->paginate();
$watchingPosts = $user->getWatchingItems(Post::class)->where('title', 'Laravel-Watching')->get();
```

### Aggregations

```php
// all
$user->watchings()->count();

// with type
$user->watchings()->withType(Post::class)->count();

// watchingers count
$post->watchingers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('watchings')->get();

foreach($users as $user) {
    echo $user->watchings_count;
}


// for Watchingable models:
$posts = Post::withCount('watchingers')->get();

foreach($posts as $post) {
    echo $post->watchings_count;
}
```

### Attach user watching status to watchingable collection

You can use `Watchinger::attachWatchingStatus($watchingables)` to attach the user watching status, it will set `has_watchinged` attribute to each model of `$watchingables`:

#### For model

```php
$post = Post::find(1);

$post = $user->attachWatchingStatus($post);

// result
[
    "id" => 1
    "title" => "Add socialite login support."
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchinged" => true
 ],
```

#### For `Collection | Paginator | CursorPaginator | array`:

```php
$posts = Post::oldest('id')->get();

$posts = $user->attachWatchingStatus($posts);

$posts = $posts->toArray();

// result
[
  [
    "id" => 1
    "title" => "Post title1"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchinged" => true
  ],
  [
    "id" => 2
    "title" => "Post title2"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchinged" => false
  ],
  [
    "id" => 3
    "title" => "Post title3"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchinged" => true
  ],
]
```

#### For pagination

```php
$posts = Post::paginate(20);

$user->attachWatchingStatus($posts);
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
// Watchinger
$users = User::with('watchings')->get();

foreach($users as $user) {
    $user->hasWatchinged($post);
}

// with watchingable object
$users = User::with('watchings.watchingable')->get();

foreach($users as $user) {
    $user->hasWatchinged($post);
}

// Watchingable
$posts = Post::with('watchings')->get();
// or
$posts = Post::with('watchingers')->get();

foreach($posts as $post) {
    $post->isWatchingedBy($user);
}
```

### Events

| **Event**                                     | **Description**                             |
| --------------------------------------------- | ------------------------------------------- |
| `Animelhd\AnimesWatching\Events\Watchinged`   | Triggered when the relationship is created. |
| `Animelhd\AnimesWatching\Events\Unwatchinged` | Triggered when the relationship is deleted. |

## License

MIT
