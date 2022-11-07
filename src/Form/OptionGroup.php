<?php

namespace pp\Form;

class OptionGroup extends Element
{
	function __construct($select, $options, ...$args)
	{

		parent::__construct(...$args);

		$this->select = $select;

		foreach ($options as $value => $label)
		{

			$e = new Option(
				label: $label, 
				attributes: [
					'value' => $value
				]
			);

			$this->elements[] = $e;

		}
	}

	function setData($data)
	{
		foreach($this->elements as $e) {
			$e->setData($data);
		}
	}


	function setName($name, $parentPrefix = null) 
	{
	}

	function getData()
	{
		
		$multiple = @$this->select->attributes['multiple']?: false;

		$data = [];
		foreach($this->elements as $e) {
			if ($value = $e->getData()) {
				if (!$multiple) {
					return $value;
				}
				$data[] = $value;
			}
		}

		return $data? $data: null;

	}

}