<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use pp\Validator\Chain;

class ArrayValues extends Validator
{

	protected Validator $validator;

	function __construct(Validator ...$validators)
	{
		$this->validator = new Chain(...$validators);
	}

	protected function validate($array)
	{

		if (!is_array($array)) {
			$this->error = dgettext('validation', 'Value is not an array');			
		}

		foreach ($array as $value) {

			if (!$this->validator->isValid($value)) {

				$this->error = $this->validator->error;
				break;
				
			}

		}

	}

}






