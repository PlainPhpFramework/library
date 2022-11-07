<?php

namespace pp\Form;

use pp\Validator;
use pp\Filter;

abstract class Element
{

	public array $attributes = [];

	public array $extra = [];

	public string $error = '';

	public bool $isValidated = false;

	public bool $isValid = false;

	protected string $name = '';

	public array $elements = [];

	protected array $validators = [];

	protected array $filters = [];

	protected $filteredData = null;

	function __construct(
		public ?string $label = null,
		$attributes = null,
		$extra = null,
		array $validators = [],
		array $filters = [],
		public ?string $help = null

	)
	{

		if ($attributes) {
			$this->attributes = $attributes + $this->attributes;
		}
		
		if ($extra) {
			$this->extra = $extra + $this->extra;
		}

		$this->setValidators($validators);
		$this->setFilters($filters);

	}

	function setName($name, $parentPrefix = null) 
	{
		$this->name = $name;
		$this->attributes['name'] = $parentPrefix? sprintf('%s[%s]', $parentPrefix, $name): $name;
		$this->attributes['id'] = str_replace(['[', ']'], ['-', ''], $this->attributes['name']);

		foreach ($this->elements as $name => $e)
		{
			$e->setName($name, $this->attributes['name']);
		}

	}

	function setData(?array $data) 
	{
		if ($this->name && array_key_exists($this->name, $data)) {
			$this->attributes['value'] = $data[$this->name];

			$this->filteredData = null;
			$this->isValidated = false;
		}
	}

	function getData() 
	{
		if ($this->filteredData === null && array_key_exists('value', $this->attributes)) {

			$data = $this->attributes['value'];
			
			$this->filteredData = $this->filters? Filter::apply($data, ...$this->filters): $data;

		}

		return $this->filteredData;

	}


	function isValid()
	{

		if (!$this->isValidated) {

			if ($this->validators && $error = Validator::apply($this->getData(), ...$this->validators)) {
				$this->error = $error;
			}

			$this->isValidated = true;
			$this->isValid = !$this->error;

		}

		return $this->isValid;

	}

	function getLabel()
	{
		return $this->label?: mb_convert_case(str_replace('_', ' ', $this->name), MB_CASE_TITLE);
	}

	protected function setFilters(array $filters)
	{
		$this->filters = $filters;
	}

	protected function setValidators(array $validators)
	{
		$this->validators = $validators;
	}


}