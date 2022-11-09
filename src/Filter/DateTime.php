<?php

namespace pp\Filter;

use pp\Validator\DateTime as Validator;

class DateTime
{

	function __construct(
		public $min = null,
		public $max = null,
		public $default = false
	)
	{
	}

	function __invoke($value)
	{

		$dateTime = date_create_immutable($value);

		if (
			!$dateTime
			|| !(new Validator(min: $this->min, max: $this->max))->isValid($dateTime)
		) {
			return $this->default;
		}

		return $dateTime;

	}

}