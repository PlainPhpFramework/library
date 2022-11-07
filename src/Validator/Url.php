<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use dgettext;
use sprintf;
use is_numeric;

class Url extends Text
{

	protected function validate($value)
	{

		parent::validate($value);

		if (!$this->error && !filter_var($value, FILTER_VALIDATE_URL)) {
			
			$this->error = dgettext('validation', 'Invalid URL');

		}

	}

}






