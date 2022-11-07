<?php

namespace pp\Form;

use pp\Validator\Text as TextValidator;
use pp\Validator\Chain as ChainValidator;

class Text extends Element
{

	public array $attributes = [
		'type' => 'text'
	];

	function getValidator() 
	{

		if (!isset($this->validatorObject)) {
			
			$validator = new TextValidator(
				required: @$this->attributes['required']?? false,
				minlength: @$this->attributes['minlength']?? null,
				maxlength: @$this->attributes['maxlength']?? null,
				pattern: @$this->attributes['pattern']?? null,
				encoding: @$this->extra['encoding']?? null,
			);

			if ($this->validators) {
				$validator = new ChainValidator($validator, ...$this->validators);
			}

			$this->validatorObject = $validator;

		}

		return $this->validatorObject;

	}

}
