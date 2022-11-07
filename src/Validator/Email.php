<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use dgettext;
use filter_var;

class Email extends Text
{

	protected function validate($value)
	{

		parent::validate($value);

		if (!$this->error && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
			
			$this->error = dgettext('validation', 'Invalid e-mail address');

		}

	}

}






