<?php

namespace pp\Filter;

use pp\Validator\Number;

class Integer
{

	function __construct(
		public ?int $min = null,
		public ?int $max = null,
		public int $step = 1,
		public ?int $default = null
	)
	{
	}

	function __invoke($value)
	{

		if (
			!is_integer($value)
			|| !(new Number(min: $this->min, max: $this->max, step: $this->step))->isValid($value)
		) {
			return $this->default;
		}

		return $value;

	}

}