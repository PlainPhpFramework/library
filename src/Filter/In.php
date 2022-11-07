<?php

namespace pp\Filter;

class In
{

	function __construct(
		public array $array = [],
		public $default = null,
		public bool $strict = false
	)
	{
	}

	function __invoke($value)
	{
		if (!in_array($value, $this->array, $this->strict)) {
			return $this->default;
		}

		return $value;

	}

}

