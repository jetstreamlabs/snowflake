<?php 

namespace JSLabs\Snowflake\Contracts;

interface SnowflakeDriver 
{
    /**
     * Get snowflake id.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Parse snowflake id.
     *
     * @param  string $id
     * @param  boolean $transform
     * @return array
     */
    public function parseId(string $id, $transform = false): array;
}
