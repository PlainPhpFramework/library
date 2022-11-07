<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use finfo;
use pathinfo;
use strtr;
use explode;
use in_array;
use substr;
use strpos;
use round;
use trim;
use is_numeric;
use strtoupper;

// https://www.php.net/manual/en/features.file-upload.errors.php
// https://www.php.net/manual/en/features.file-upload.post-method.php

class File extends Validator
{

	function __construct(
		public bool $required = true,
		public ?string $minsize = null,
		public ?string $maxsize = null,
		public ?string $accept = null
	)
	{

		if ($minsize) {
			$this->minsize = $this->fromByteString($minsize);
		}

		if ($maxsize) {
			$this->maxsize = $this->fromByteString($maxsize);
		}

	}

	protected function validate($filePath)
	{

		if (!file_exists($filePath)) {

			$this->error = dgettext('validation', 'The file does not exist');

		} 
		elseif (!is_readable($filePath)) {

			$this->error = dgettext('validation', 'The file is not readable');

		}
		else {

			$fileSize = filesize($filePath);

			if ($fileSize === 0) {

				$this->error = dgettext('validation', 'The file is empty');

			}
			elseif($this->maxsize && $filesize > $this->maxsize) {

				$this->error = strtr(
					dgettext('validation', 'Maximum allowed size for file is "%maxsize%" but "%size%" detected'), 
					['%maxsize%' => $this->toByteString($this->maxsize), '%size%' => $this->toByteString($filesize)]
				);

			}
			elseif($this->minsize && $filesize < $this->minsize) {

				$this->error = strtr(
					dgettext('validation', 'Minimum expected size for file is "%minsize%" but "%size%" detected'), 
					['%minsize%' => $this->toByteString($this->minsize), '%size%' => $this->toByteString($filesize)]
				);

			}


			$finfo = new finfo();
			$validExtensions = explode('/', $finfo->file($filePath, FILEINFO_EXTENSION));
			$extension = pathinfo($filePath, PATHINFO_EXTENSION);
			if (!in_array($extension, $validExtensions)) {
				$this->error = dgettext('validation', "File extension does not match MIME type");					
			}

			if ($this->accept) {

				$accept = explode(',', $this->accept);

				$mime = $finfo->file($filePath, FILEINFO_MIME_TYPE);

				$isValid = false;
				foreach ($accept as $fileType) {

					$fileType = strtolower(trim($fileType));

					if (substr($fileType, 0, 1) === '.') {

						if (substr($fileType, 1) === $extension) {
							$isValid = true;
							break;
						}

					} 
					elseif (substr($fileType, -2) === '/*') {

						if (strpos($mime, substr($fileType, 0, -1)) === 0) {
							$isValid = true;
							break;
						}

					}
					else {

						if ($mime === $fileType) {
							$isValid = true;
							break;
						}

					}

				}

				if (!$isValid) {
					$this->error = dgettext('validation', 'The file type is not permitted');
				}

			}

		}

	}

    /**
     * Returns the formatted size 
     * (c) Laminas validator
     *
     * @param  int $size
     * @return string
     */
    protected function toByteString($size)
    {
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        for ($i = 0; $size >= 1024 && $i < 9; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $sizes[$i];
    }

    /**
     * Returns the unformatted size
     *
     * @param string $size
     * @return float|int|string
     */
    protected function fromByteString($size)
    {
        if (is_numeric($size)) {
            return (int) $size;
        }

        $type = trim(substr($size, -1));
        $value = (int) $size;

        switch (strtoupper($type)) {
            case 'Y':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'Z':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'E':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'P':
                $value *= 1024 * 1024 * 1024 * 1024 * 1024;
                break;
            case 'T':
                $value *= 1024 * 1024 * 1024 * 1024;
                break;
            case 'G':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'M':
                $value *= 1024 * 1024;
                break;
            case 'K':
                $value *= 1024;
                break;
            default:
                break;
        }

        return $value;
    }

}






