<?php

namespace pp\Form;

use pp\Validator\Token as Validator;
use LogicException;

class Token extends Hidden
{

	public array $attributes = [
		'type' => 'hidden'
	];

	function __construct(protected $token = null, $session_key = 'csrf-token') 
	{
		if (!$this->token) {
			$this->token = @$_SESSION[$session_key] or throw new LogicException('Csrf token not found');
		}

		$this->attributes['value'] = $this->token;
	}

	function getData()
	{
	}

	function setValidators(array $validators)
	{

		$this->validators = [new Validator($this->token)];
	}

}
