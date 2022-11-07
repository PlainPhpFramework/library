<?php

namespace pp\Form;

class Checkbox extends Element
{

	public array $attributes = [
		'type' => 'checkbox',
		'value' => '1'
	];

	function setData(?array $data) 
	{

		$this->attributes['checked'] = false;

		if ($data) {

			$value = $this->attributes['value'];

			if ($this->name && array_key_exists($this->name, $data) && $value == $data[$this->name]) {
				$this->attributes['checked'] = true;
			}

		}

	}

	function getData() 
	{
		if (@$this->attributes['checked']) {
			return $this->attributes['value'];
		}
	}

}
