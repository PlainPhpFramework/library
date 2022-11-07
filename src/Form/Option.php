<?php

namespace pp\Form;

class Option extends Element
{

	function setData($data = '')
	{

		$value = $this->attributes['value'];

		// Multiselect data
		if (is_array($data)) {
			$this->attributes['selected'] = in_array($value, $data);
		} else {
			$this->attributes['selected'] = $value == $data;
		}

	}

	function getData() 
	{
		if (@$this->attributes['selected']) {
			return $this->attributes['value'];
		}
	}

}
