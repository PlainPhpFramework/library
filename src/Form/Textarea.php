<?php

namespace pp\Form;

class Textarea extends Text
{
	
	public array $attributes = [];

	public ?string $value = '';

	function setData(?array $data) 
	{

		if ($this->name && array_key_exists($this->name, $data)) {
			$this->value = $data[$this->name];
		}
	}

	function getData()
	{
		return $this->value;
	}

}