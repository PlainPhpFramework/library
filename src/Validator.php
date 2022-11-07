<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp;

use dgettext;
use bindtextdomain;
use realpath;
use is_array;
use is_scalar;

abstract class Validator
{

	/**
	 * @var string The first error encountered by the validator 
	 */
	public string $error = '';

	/**
	 * @var bool Allow or disallow null and empty string to be a valid value
	 */
	public bool $required = true;

	function isValid($value)
	{

		$this->error = '';

		if ($value === '' || !is_scalar($value) && empty($value)) {

			if ($this->required) {

				$this->error = dgettext('validation', 'Value cannot be empty');

			}

		} else {

			$this->validate($value);

		}
		
		return !$this->error;

	}

	static function apply($value, Validator ...$validators)
	{
		$validator = new Validator\Chain(...$validators);
		$validator->isValid($value);
		return $validator->error;
	}

	static function array(?array $values, array $meta)
	{

		$output = [];

		foreach ($meta as $key => $validators) {
			$validators = is_array($validators)? $validators: [$validators];
			if ($error = static::apply(@$values[$key], ...$validators)) {
				$output[$key] = $error;
			}
		}

		return $output;

	}

	abstract protected function validate($value);

}