<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use DateTimeInterface;
use Locale;
use LogicException;
use dgettext;
use sprintf;
use date_create_immutable;

class DateTime extends Validator
{

	function __construct(
		public $min = null,
		public $max = null,
	)
	{
	}

	protected function validate($dateTime)
	{

		if ($this->min && !($this->min instanceof DateTimeInterface)) {
			$this->min = date_create_immutable($this->min) 
				or throw new LogicException('Min cannot be converted to datetime. Min was: '. $this->min);
		}

		if ($this->max && !($this->max instanceof DateTimeInterface)) {
			$this->max = date_create_immutable($this->max) 
				or throw new LogicException('Max cannot be converted to datetime. Max was: '. $this->max);
		}


		if (!($dateTime instanceof DateTimeInterface)) {

			$dateTime = date_create_immutable($dateTime);

		}

		if (!$dateTime) {

			$this->error = dgettext('validation', 'Invalid datetime');

		} elseif($this->min && $dataTime < $this->min) {

			$this->error = sprintf(dgettext('validation', 'Datetime must be greater than or equal to %s'), IntlDateFormatter::formatObject(
				$this->min,
				[IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM],
				Locale::getDefault()
			));

		} elseif($this->max && $dataTime > $this->max) {

			$this->error = sprintf(dgettext('validation', 'Datetime must be less than or equal to %s'), IntlDateFormatter::formatObject(
				$this->max,
				[IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM],
				Locale::getDefault()
			));

		}

	}

}