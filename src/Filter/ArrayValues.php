<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Filter;

class ArrayValues
{
	
	public $filters;

	function __construct(Callable ...$filters)
	{
		$this->filters = $filters;
	}

	function __invoke($value)
	{

		if (!is_array($value)) {
			$value = [$value];
		}

		return Filter::apply($value, ...$this->filter);

	}

}






