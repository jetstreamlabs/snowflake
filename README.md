# JestreamLabs Snowflake

<div>
  <p align="center">
    <image src="https://www.pngkey.com/png/full/105-1052235_snowflake-png-transparent-background-snowflake-with-clear-background.png" width="250" height="250">
  </p>
  <p align="center">An ID Generator for PHP based on Snowflake Algorithm (Twitter announced).</p>
</div>

## Description

Snowflake is a network service for generating unique ID numbers at high scale with some simple guarantees.

* The first bit is unused sign bit.
* The second part consists of a 41-bit timestamp (milliseconds) whose value is the offset of the current time relative to a certain time.
* The 5 bits of the third and fourth parts represent data center and worker, and max value is 2^5 -1 = 31.
* The last part consists of 12 bits, its means the length of the serial number generated per millisecond per working node, a maximum of 2^12 -1 = 4095 IDs can be generated in the same millisecond.
* In a distributed environment, five-bit datacenter and worker mean that can deploy 31 datacenters, and each datacenter can deploy up to 31 nodes.
* The binary length of 41 bits is at most 2^41 -1 millisecond = 69 years. So the snowflake algorithm can be used for up to 69 years, In order to maximize the use of the algorithm, you should specify a start time for it.

> You must know, The ID generated by the snowflake algorithm is not guaranteed to be unique.
> For example, when two different requests enter the same node of the same data center at the same time, and the sequence generated by the node is the same, the generated ID will be duplicated.

So if you want use the snowflake algorithm to generate unique ID, You must ensure: The sequence-number generated in the same millisecond of the same node is unique.
Based on this, we created this package and integrated multiple sequence-number providers into it.

* RandomSequenceResolver (Random)
* RedisSequenceResolver (based on redis psetex and incrby)
* LaravelSequenceResolver (based on redis psetex and incrby)
* SwooleSequenceResolver (based on swoole_lock)

> Each provider only needs to ensure that the serial number generated in the same millisecond is different. You can get a unique ID.

## Requirement

1. PHP >= 8.0
2. **[Composer](https://getcomposer.org/)**

## Installation

```shell
$ composer require jetstreamlabs/snowflake 
```

## Useage

Coming soon ...

## Advanced

Coming soon ...

## License

MIT

Ported and enhanced from the [Godruoyi](https://github.com/godruoyi/php-snowflake) repository package.

Original Godruoyi License file included in LICENSES directory.