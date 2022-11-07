<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp;

use is_array;

/**
 * Usage:
 * 
 * Filter::apply($var, 'trim', 'strtolower');
 * Filter::array($array, [
 *   'var1' => ['trim', 'strtolower'],
 *   'var2' => ['strtoupper'],
 * ]);
 */

class Filter
{

	static function apply($value, Callable ...$filters)
	{
		foreach ($filters as $filter) {
			$value = $filter($value);
		}

		return $value;
	}

	static function array(?array $values, array $meta)
	{
		
		$output = [];

		foreach ($meta as $key => $filters) {
			$filters = is_array($filters)? $filters: [$filters];
			$output[$key] = static::apply(@$values[$key], ...$filters);
		}

		return $output;

	}

}
