<?php 

use JSLabs\Snowflake\Snowflake;
use JSLabs\Snowflake\Sonyflake;
use JSLabs\Snowflake\Resolvers\RedisResolver;
use JSLabs\Snowflake\Resolvers\RandomResolver;
use JSLabs\Snowflake\Resolvers\SwooleResolver;
use JSLabs\Snowflake\Resolvers\LaravelResolver;

return [
    // Requested Snowflake instance.
    // options: sonyflake | snowflake (default)
    'instance' => env('SNOWFLAKE_INSTANCE', 'snowflake'),

    // list of all available Snowflake drivers.
    'drivers' => [
        'snowflake' => Snowflake::class,
        'sonyflake' => Sonyflake::class
    ],

    // Constructor options (all must be integers)
    'datacenter' => env('SNOWFLAKE_DATACENTER', 0),
    'worker' => env('SNOWFLAKE_WORKER', 0),
    // 0 ~ 65535
    'machine' => env('SNOWFLAKE_MACHINE', 0),

    // Requested Snowflake Resolver.
    // options: laravel | redis | swoole | random (default)
    'resolver' => env('SNOWFLAKE_RESOLVER', 'random'),
    
    // List all available SequenceResolvers.
    'providers' => [
        // Requires Laravel v8.* installed.
        'laravel' => LaravelResolver::class,
        'random'  => RandomResolver::class,
        'redis'   => RedisResolver::class,
        'swoole'  => SwooleResolver::class
    ],

    // Start timestamp (must be a valid timestamp) 
    'start' => env('SNOWFLAKE_START', time() * 1000),
];