<?php

namespace Jetlabs\Snowflake\Resolvers;

use Illuminate\Contracts\Cache\Repository;
use Jetlabs\Snowflake\Contracts\SequenceResolver;

class LaravelResolver implements SequenceResolver
{
  /**
   * The laravel cache instance.
   *
   * @var \Illuminate\Contracts\Cache\Repository
   */
  protected Repository $cache;

  /**
   * Init resolve instance, must connectioned.
   */
  public function __construct(Repository $cache)
  {
    $this->cache = $cache;
  }

  /**
   * Increment the sequence.
   *
   * @param  int  $currentTime
   * @return int
   */
  public function sequence(int $currentTime): int
  {
    $key = $currentTime;

    if ($this->cache->add($key, 1, 1)) {
      return 0;
    }

    return $this->cache->increment($key, 1);
  }
}
