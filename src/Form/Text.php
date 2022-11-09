<?php

namespace pp\Form;

use pp\Validator\Text as TextValidator;

class Text extends Element
{

	public array $attributes = [
		'type' => 'text'
	];

	function setValidators(array $validators) 
	{

		array_unshift($validators, new TextValidator(
			required: @$this->attributes['required']?? false,
			minlength: @$this->attributes['minlength']?? null,
			maxlength: @$this->attributes['maxlength']?? null,
			pattern: @$this->attributes['pattern']?? null,
			encoding: @$this->extra['encoding']?? null,
		));

		$this->validators = $validators;

	}

}
