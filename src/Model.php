<?php

namespace Jetlabs\Snowflake;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jetlabs\Snowflake\Concerns\HasSnowflakePrimary;

abstract class Model extends EloquentModel
{
	use HasSnowflakePrimary;

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Retrieve the model for a bound value.
	 *
	 * @param  mixed  $value
	 * @param  string|null  $field
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function resolveRouteBinding($value, $field = null)
	{
		return in_array(SoftDeletes::class, class_uses($this))
			? $this->where($field ?? $this->getRouteKeyName(), $value)->withTrashed()->first()
			: parent::resolveRouteBinding($value, $field);
	}

	/**
	 * Return self to ensure proper error handling.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function getEntity()
	{
		return $this;
	}
}
