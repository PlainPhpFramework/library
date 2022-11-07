<?php

namespace pp\Filter;

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

		$value = (int) $value;

		if (
			($this->min && $value < $this->min)
			|| ($this->max && $value > $this->max)
			|| ($this->step && $value % $this->step !== 0)
		) {
			return $this->default;
		}

		return $value;

	}

}