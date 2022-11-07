<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use in_array;

class In extends Validator
{

	function __construct(
		public bool $required = true,
		public array $array = [],
		public bool $strict = false
	)
	{
	}

	function validate($value)
	{
		if (!in_array($value, $this->array, $this->strict)) {
			$this->error = dgettext('validation', 'Invalid value');
		}

	}

}

