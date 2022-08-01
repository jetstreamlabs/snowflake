<?php

namespace Jetlabs\Snowflake\Resolvers;

use Jetlabs\Snowflake\Contracts\SequenceResolver;

class RandomResolver implements SequenceResolver
{
  /**
   * The last timestamp.
   *
   * @var int|null
   */
  protected ?int $lastTimeStamp = -1;

  /**
   * The sequence.
   *
   * @var int
   */
  protected int $sequence = 0;

  /**
   * Increment the sequence.
   *
   * @param  int  $currentTime
   * @return int
   */
  public function sequence(int $currentTime): int
  {
    if ($this->lastTimeStamp === $currentTime) {
      $this->sequence++;
      $this->lastTimeStamp = $currentTime;

      return $this->sequence;
    }

    $this->sequence = 0;
    $this->lastTimeStamp = $currentTime;

    return 0;
  }
}
