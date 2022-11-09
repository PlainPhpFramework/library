<?php

namespace pp\Form;

class Select extends Element
{
	function __construct($options, ...$args)
	{

		parent::__construct(...$args);

		foreach ($options as $value => $label)
		{

			// Optiongroup
			if (is_array($label)) {

				$e = new OptionGroup($this, label: $value, options: $label);

			} else {
				$e = new Option(
					label: $label, 
					attributes: [
						'value' => $value
					]
				);
			}

			$this->elements[] = $e;

		}
	}

	function setData(?array $data)
	{

		$multiple = @$this->attributes['multiple']?: false;

		if ($this->name && @$data && array_key_exists($this->name, $data) 
			&& (
				($multiple && is_array($data[$this->name]))
				|| (!$multiple && !is_array($data[$this->name]))
			)) {
			foreach($this->elements as $e) {
				$e->setData($data[$this->name]);
			}
		}
	}


	function setName($name, $parentPrefix = null) 
	{
		$this->name = $name;
		$this->attributes['name'] = $parentPrefix? sprintf('%s[%s]', $parentPrefix, $name): $name;
		$this->attributes['id'] = str_replace(['[', ']'], ['-', ''], $this->attributes['name']);
	}

	function getData()
	{
		
		$multiple = @$this->attributes['multiple']?: false;

		$data = [];
		foreach($this->elements as $e) {
			if ($value = $e->getData()) {
				if (!$multiple) {
					return $value;
				}
				if (is_array($value)) {
					$data = array_merge($data, $value);
				} else {
					$data[] = $value;
				}
			}
		}

		return $data? $data: null;

	}

}