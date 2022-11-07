<?php

namespace pp\Form;

use pp\Validator\Email as EmailValidator;
use pp\Validator\Chain as ChainValidator;
use pp\Validator\ArrayValues as ArrayValidator;
use pp\Filter\ArrayValues as ArrayFilter;

class Email extends Text
{

	public array $attributes = [
		'type' => 'email'
	];

	function setFilters(array $filters)
	{

		if(@$this->attributes['multiple'] === true) {

			if ($filters) {
				$filters = [new ArrayFilter(...$filters)];
			}

			array_unshift($filters, function($data) {
				return array_map('trim', explode(',', $data));
			});

		}

		$this->filters = $filters;
		
	}

	function setValidators(array $validators) 
	{

		array_unshift($validators, new EmailValidator(
			required: @$this->attributes['required']?? false,
			minlength: @$this->attributes['minlength']?? null,
			maxlength: @$this->attributes['maxlength']?? null,
			pattern: @$this->attributes['pattern']?? null,
			encoding: @$this->extra['encoding']?? null,
		));
		
		if (@$this->attributes['multiple'] === true) {
			$validators = [new ArrayValidator(...$validators)];	
		}

		$this->validators = $validators;
	}

}