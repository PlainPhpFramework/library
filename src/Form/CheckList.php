<?php

namespace pp\Form;

class CheckList extends Element
{
	function __construct($list, ...$args)
	{

		parent::__construct(...$args);

		foreach ($list as $value => $label)
		{
			$e = new Checkbox(
				label: $label, 
				attributes: [
					'value' => $value
				]
			);

			$e->setName($value);

			$this->elements[$value] = $e;

		}
	}

	function setData(?array $data)
	{

		if ($this->name && @$data[$this->name] && is_array($data[$this->name])) {
			foreach($this->elements as $e) {
				$e->setData($data[$this->name]);
			}
		}
	}

	function getData()
	{
		$data = [];
		foreach($this->elements as $e) {
			if ($value = $e->getData()) {
				$data[] = $value;
			}
		}

		return $data? $data: null;
	}

}