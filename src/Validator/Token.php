<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use LogicException;
use dgettext;

class Token extends Validator
{

	function __construct(protected $token = null, $session_key = 'csrf-token') 
	{
		if (!$this->token) {
			$this->token = @$_SESSION[$session_key] or throw new LogicException('Csrf token not found');
		}
	}

	protected function validate($token)
	{
		if (!hash_equals($token, $this->token)) {
			$this->error = dgettext('validation', 'Invalid csrf token');
		}
	}

}








