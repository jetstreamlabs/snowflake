<?php

namespace Jetlabs\Snowflake\Providers;

use Illuminate\Support\ServiceProvider;
use Jetlabs\Snowflake\Contracts\SequenceResolver;
use Jetlabs\Snowflake\Contracts\SnowflakeDriver;
use Jetlabs\Snowflake\Snowflake;
use Jetlabs\Snowflake\Sonyflake;

class SnowflakeServiceProvider extends ServiceProvider
{
  /**
   * Register any package services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/../../config/snowflake.php',
      'snowflake'
    );
  }

  /**
   * Boot any package services.
   *
   * @return void
   */
  public function boot()
  {
    $this->publishes([
      __DIR__.'/../../config/snowflake.php' => config_path('snowflake.php'),
    ]);

    $this->registerProviders();
  }

  /**
   * Bind the service to the container.
   *
   * @return void
   */
  public function registerProviders()
  {
    $instance = $this->app['config']->get('snowflake.instance');

    $this->app->bind($instance, function ($app) use ($instance) {
      return $this->buildProviderInstance($instance);
    });
  }

  /**
   * Build an instance of the Snowflake/Sonyflake class.
   *
   * @param  string  $instance
   * @return void
   */
  public function buildProviderInstance($instance)
  {
    $timestamp = $this->app['config']->get('snowflake.start');
    $resolver = $this->app['config']->get('snowflake.resolver');

    $driver = $this->resolveDriver($instance);

    return $driver->setStartTimeStamp($timestamp)
      ->setSequenceResolver($this->resolveInstance($resolver));
  }

  /**
   * Configure the resolver via config.
   *
   * @param  string  $key
   * @return string
   */
  private function configureResolver(string $key): string
  {
    $providers = $this->app['config']->get('snowflake.providers');

    return $providers[$key];
  }

  /**
   * Configure the driver via config.
   *
   * @param  string  $key
   * @return string
   */
  private function configureDriver(string $key): string
  {
    $drivers = $this->app['config']->get('snowflake.drivers');

    return $drivers[$key];
  }

  /**
   * Resolve the requested SequenceResolver class.
   *
   * @param  string  $key
   * @return \Jetlabs\Snowflake\Contracts\SequenceResolver
   */
  private function resolveInstance(string $key): SequenceResolver
  {
    $class = $this->configureResolver($key);

    switch ($key) {
      case 'laravel':
        $resolver = new $class($this->app['cache']->store());
        break;

      case 'random':
        $resolver = new $class(time());
        break;

      case 'redis':
        $resolver = new $class(app('redis'));
        break;

      case 'swoole':
        $resolver = new $class(time());
        break;
    }

    return $resolver;
  }

  /**
   * Resolve and build the requested Driver.
   *
   * @param  string  $key
   * @return \Jetlabs\Snowflake\Contracts\SnowflakeDriver
   */
  private function resolveDriver(string $key): SnowflakeDriver
  {
    $class = $this->configureDriver($key);
    $config = $this->app['config']->get('snowflake');

    switch ($key) {
      case 'snowflake':
        $driver = new $class($config['datacenter'], $config['worker']);
        break;
      case 'sonyflake':
        $driver = new $class($config['machine']);
        break;
    }

    return $driver;
  }
}
