<?php

namespace JetLabs\Snowflake\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasSnowflakePrimary
{
	public static function bootHasSnowflakePrimary()
	{
		static::saving(function (Model $model) {
			if (is_null($model->getKey())) {
				$model->setIncrementing(false);

				$model->setAttribute(
		  $model->getKeyName(),
		  app(config('snowflake.instance'))->id()
		);
			}
		});
	}
}
