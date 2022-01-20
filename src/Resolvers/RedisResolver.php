<?php

namespace JetLabs\Snowflake\Resolvers;

use JetLabs\Snowflake\Contracts\SequenceResolver;
use Redis;
use RedisException;

class RedisResolver implements SequenceResolver
{
	/**
	 * The redis client instance.
	 *
	 * @var \Redis
	 */
	protected $redis;

	/**
	 * The cache prefix.
	 *
	 * @var string
	 */
	protected string $prefix = '';

	/**
	 * Init resolve instance, must connectioned.
	 */
	public function __construct($redis)
	{
		if ($redis->ping()) {
			$this->redis = $redis;

			return;
		}

		throw new RedisException('Redis server went away');
	}

	/**
	 * Increment the sequence.
	 *
	 * @param  int  $currentTime
	 * @return int
	 */
	public function sequence(int $currentTime): int
	{
		$lua = "return redis.call('exists',KEYS[1])<1 and redis.call('psetex',KEYS[1],ARGV[2],ARGV[1])";

		$key = $this->prefix.$currentTime;

		if ($this->redis->eval($lua, [$key, 1, 1000], 1)) {
			return 0;
		}

		return $this->redis->incrby($key, 1);
	}

	/**
	 * Set cache prefix.
	 *
	 * @param  string  $prefix
	 * @return \JetLabs\Snowflake\RedisResolver
	 */
	public function setCachePrefix(string $prefix): RedisResolver
	{
		$this->prefix = $prefix;

		return $this;
	}
}
