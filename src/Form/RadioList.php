<?php

namespace pp\Form;

class RadioList extends Element
{
	function __construct($list, ...$args)
	{

		parent::__construct(...$args);

		foreach ($list as $value => $label)
		{
			$e = new Radio(
				label: $label, 
				attributes: [
					'value' => $value
				]
			);

			$this->elements[] = $e;

		}
	}

	function setName($name, $parentPrefix = null) 
	{
		$this->name = $name;
		$this->attributes['name'] = $parentPrefix? sprintf('%s[%s]', $parentPrefix, $name): $name;

		$i = 0;
		foreach ($this->elements as $name => $e)
		{
			$e->setName($this->attributes['name']);
			$e->attributes['id'] .= '-'.(++$i);
		}

	}

	function setData(?array $data)
	{
		if ($this->name && @$data[$this->name]) {
			foreach($this->elements as $e) {
				$e->setData($data);
			}
		}
	}

	function getData()
	{
		foreach($this->elements as $e) {
			if ($value = $e->getData()) {
				return $value;
			}
		}
	}

}