<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use dgettext;
use sprintf;
use is_numeric;

class Number extends Validator
{

	function __construct(
		public bool $required = true,
		public ?int $min = null,
		public ?int $max = null,
		public ?int $step = null,
	)
	{
	}

	protected function validate($value)
	{

		if (!is_numeric($value)) {

			$this->error = dgettext('validation', 'Value must be a number');

		} 

		elseif ($this->min && $value < $this->min) {

			$this->error = sprintf(dgettext('validation', 'Value must be greater than or equal to %s'), $this->min);

		}

		elseif ($this->max && $value > $this->max) {

			$this->error = sprintf(dgettext('validation', 'Value must be less than or equal to %s'), $this->max);

		}

		elseif ($this->step && ($modulo = ($value - $this->min) % $this->step) !== 0) {

			$prev = $value - $modulo;
			$next = $prev + $this->step;

			$this->error = sprintf(dgettext('validation', 'Invalid value. The two nearest valid values are %s and %s'), $prev, $next);

		}


	}

}


