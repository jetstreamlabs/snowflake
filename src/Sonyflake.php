<?php

namespace Jetlabs\Snowflake;

use Jetlabs\Snowflake\Contracts\SnowflakeDriver;

class Sonyflake extends Snowflake implements SnowflakeDriver
{
	const MAX_TIMESTAMP_LENGTH = 39;

	const MAX_MACHINEID_LENGTH = 16;

	const MAX_SEQUENCE_LENGTH = 8;

	/**
	 * The machine ID.
	 *
	 * @var int
	 */
	protected int $machineid;

	/**
	 * Build Sonyflake Instance.
	 *
	 * @param  int  $machineid  machine ID 0 ~ 65535 (2^16)-1
	 */
	public function __construct(int $machineid = 0)
	{
		$maxMachineID = -1 ^ (-1 << self::MAX_MACHINEID_LENGTH);

		$this->machineid = $machineid;
		if ($this->machineid < 0 || $this->machineid > $maxMachineID) {
			throw new \InvalidArgumentException("Invalid machine ID, must be between 0 ~ {$maxMachineID}.");
		}
	}

	/**
	 * Get Sonyflake id.
	 *
	 * @return string
	 */
	public function id(): string
	{
		$elapsedTime = $this->elapsedTime();

		while (($sequence = $this->callResolver($elapsedTime)) > (-1 ^ (-1 << self::MAX_SEQUENCE_LENGTH))) {
			$elapsedTime2 = $this->elapsedTime();
			// Get next timestamp
			while ($elapsedTime2 == $elapsedTime) {
				usleep(1);
				$elapsedTime2 = $this->elapsedTime();
			}
			$elapsedTime = $elapsedTime2;
		}

		$machineidLeftMoveLength = self::MAX_SEQUENCE_LENGTH;
		$timestampLeftMoveLength = self::MAX_MACHINEID_LENGTH + $machineidLeftMoveLength;

		if ($elapsedTime > (-1 ^ (-1 << self::MAX_TIMESTAMP_LENGTH))) {
			// The lifetime (174 years).
			throw new \Exception('Exceeding the maximum life cycle of the algorithm.');
		}

		return (string) ($elapsedTime << $timestampLeftMoveLength
			| ($this->machineid << $machineidLeftMoveLength)
			| ($sequence));
	}

	/**
	 * Set start time (millisecond).
	 *
	 * @param  int  $startTime
	 * @return \Jetlabs\Snowflake\Sonyflake
	 */
	public function setStartTimeStamp(int $startTime): Sonyflake
	{
		$elapsedTime = floor(($this->getCurrentMicrotime() - $startTime) / 10) | 0;
		if ($elapsedTime < 0) {
			throw new \Exception('The start time cannot be greater than the current time');
		}

		$maxTimeDiff = -1 ^ (-1 << self::MAX_TIMESTAMP_LENGTH);
		if ($elapsedTime > $maxTimeDiff) {
			throw new \Exception('Exceeding the maximum life cycle of the algorithm');
		}

		$this->startTime = $startTime;

		return $this;
	}

	/**
	 * Parse snowflake id.
	 *
	 * @param  string  $id
	 * @param  bool  $transform
	 * @return array
	 */
	public function parseId(string $id, $transform = false): array
	{
		$id = decbin($id);
		$length = self::MAX_SEQUENCE_LENGTH + self::MAX_MACHINEID_LENGTH;

		$data = [
			'sequence' => substr($id, -1 * self::MAX_SEQUENCE_LENGTH),
			'machineid' => substr($id, -1 * $length, self::MAX_MACHINEID_LENGTH),
			'timestamp' => substr($id, 0, $length),
		];

		return $transform ? array_map(function ($value) {
			return bindec($value);
		}, $data) : $data;
	}

	/**
	 * The Elapsed Time.
	 *
	 * @return int
	 */
	private function elapsedTime(): int
	{
		return floor(($this->getCurrentMicrotime() - $this->getStartTimeStamp()) / 10) | 0;
	}
}
