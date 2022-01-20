<?php

namespace JetLabs\Snowflake\Resolvers;

use JetLabs\Snowflake\Contracts\SequenceResolver;

class SwooleResolver implements SequenceResolver
{
	/**
	 * The last timestamp.
	 *
	 * @var null
	 */
	protected ?int $lastTimeStamp = -1;

	/**
	 * The sequence.
	 *
	 * @var int
	 */
	protected int $sequence = 0;

	/**
	 * The swoole lock.
	 *
	 * @var mixed
	 */
	protected $lock;

	/**
	 * The cycle count.
	 *
	 * @var int
	 */
	protected int $count = 0;

	/**
	 * Init swoole lock.
	 */
	public function __construct()
	{
		$this->lock = new \swoole_lock(SWOOLE_MUTEX);
	}

	/**
	 * Increment the sequence.
	 *
	 * @param  int  $currentTime
	 * @return int
	 */
	public function sequence(int $currentTime): int
	{
		/*
		 * If swoole lock failure，we return a bit number, This will cause the program to
		 * perform the next millisecond operation.
		 */
		if (! $this->lock->trylock()) {
			if ($this->count >= 10) {
				throw new \Exception('Swoole lock failure, Unable to get the program lock after many attempts.');
			}

			$this->count++;

			return 999999;
		}

		if ($this->lastTimeStamp === $currentTime) {
			$this->sequence++;
		} else {
			$this->sequence = 0;
		}

		$this->lastTimeStamp = $currentTime;

		$this->lock->unlock();

		return $this->sequence;
	}
}
