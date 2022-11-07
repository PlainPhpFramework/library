<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use dgettext;
use sprintf;
use is_string;
use mb_strlen;
use preg_match;
use str_replace;


class Text extends Validator
{

	function __construct(
		public bool $required = true,
		public ?int $minlength = null,
		public ?int $maxlength = null,
		public ?string $pattern = null,
		public ?string $encoding = 'UTF-8'
	)
	{
	}

	protected function validate($value)
	{

		if (!is_string($value)) {

			$this->error = dgettext('validation', 'Value must be a string');

		} else {

			$length = mb_strlen($value, $this->encoding);

			if ($this->minlength && $length < $this->minlength) {

				$this->error = sprintf(dgettext('validation', 'Value must be greater than or equal to %s characters'), $this->minlength);

			}

			elseif ($this->maxlength && $length > $this->maxlength) {

				$this->error = sprintf(dgettext('validation', 'Value must be less than or equal to %s characters'), $this->maxlength);

			}

			elseif ($this->pattern && !preg_match('/^'.str_replace(['^', '/', '$'], ['\^', '\/', '\$'], $this->pattern).'$/', $value)) {

				$this->error = dgettext('validation', 'Value does not match the requested format');

			}

		}

		
	}

}

