<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;

class Chain extends Validator
{

	public $validators;

	function __construct(Validator ...$validators)
	{
		$this->required = false;
		$this->validators = $validators;
	}

	protected function validate($value)
	{

		foreach ($this->validators as $validator) {

			if (!$validator->isValid($value)) {
				$this->error = $validator->error;
				break;
			}

		}

	}

}






