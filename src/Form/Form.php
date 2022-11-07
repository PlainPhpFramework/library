<?php

namespace pp\Form;

class Form extends Element
{

	public array $attributes = [
		'method' => 'post'
	];

	public array $errors = [];

	function add($name, Element $element) 
	{

		$element->setName($name);
		$this->elements[$name] = $element;

		return $this;

	}

	function setData(?array $data)
	{

		if ($this->name) {
			$data = @$data[$this->name]?: [];
		}

		foreach($this->elements as $e) {
			$e->setData($data);
		}

	}

	function getData()
	{

		$data = [];

		foreach($this->elements as $name => $e) {

			if ($value = $e->getData()) {
				$data[$name] = $value;
			}				

		}

		return $data;

	}


	function isValid()
	{
		
		$isValid = true;

		foreach ($this->elements as $name => $element) {

			if (!$element->isValid()) {
	
				if($isValid) {
					$isValid = false;
					$this->error = dgettext('validation', 'The form is invalid. Please make sure that all fields are correct');
				}

				$this->errors[$name] = $element->error;

			}


		}

		return $isValid;
	}

}