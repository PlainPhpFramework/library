<?php

namespace pp\Form;

use pp\Validator\DateTime as DateTimeValidator;
use pp\Filter\DateTime as DateTimeFilter;

class DateTime extends Element
{

	public array $attributes = [
		'type' => 'datetime-local',
		'pattern' => '[0-9]{4,}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}', // Fallback for browser not supporting datetime-local
	];


	function setFilters(array $filters)
	{
		array_unshift($filters, new DateTimeFilter(
			default: false,
		));
		$this->filters = $filters;	
	}

	function setValidators(array $validators) 
	{

		array_unshift($validators, new DateTimeValidator(
			min: isset($this->attributes['min'])? $this->attributes['min']: null,
			max: isset($this->attributes['max'])? $this->attributes['max']: null,
		));

		$this->validators = $validators;

	}

}

