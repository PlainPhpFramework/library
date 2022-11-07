<?php
/**
 * (c) Francesco Terenzani
 */

namespace pp\Validator;

use pp\Validator;
use dgettext;
use error_log;
use strtr;
use is_uploaded_file;

// https://www.php.net/manual/en/features.file-upload.errors.php
// https://www.php.net/manual/en/features.file-upload.post-method.php

class Upload extends File
{

	protected function validate(array $uploadedFile)
	{
		$errors = array(
		    0 => 'There is no error, the file uploaded with success',
		    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		    3 => 'The uploaded file was only partially uploaded',
		    4 => 'No file was uploaded',
		    6 => 'Missing a temporary folder',
		    7 => 'Failed to write file to disk.',
		    8 => 'A PHP extension stopped the file upload.',
		);

		if (@$uploadedFile['error']) {

			switch ($uploadedFile['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$this->error = strstr(
						dgettext('validation', 'Maximum allowed size for file is "%maxsize%" but "%size%" detected'),
						[
							'%size%' => $this->toByteString($uploadedFile['size']), 
							'%maxsize%' => $this->toByteString($this->fromByteString(ini_get('upload_max_filesize')))
						]
					);
				case UPLOAD_ERR_FORM_SIZE:
					$this->error = strstr(
						dgettext('validation', 'Maximum allowed size for file is "%maxsize%" but "%size%" detected'),
						[
							'%size%' => $this->toByteString($uploadedFile['size']), 
							'%maxsize%' => $this->toByteString(@$_POST['MAX_FILE_SIZE'])
						]
					);
					break;
				
				default:
					$this->error = dgettext('validation', 'Failed to upload the file');
					break;
			}

			\error_log('Upload error: ' . @$errors[$uploadedFile['error']]);

		} else {

			if (!isset($uploadedFile['tmp_name'])) {
				$this->error = gettext('validation', 'Missing a tmp_name');
			}

			elseif (!is_uploaded_file($uploadedFile['tmp_name'])) {
				$this->error = gettext('validation', 'The file is not an uploaded one');
			} 

			else {
				parent::validate($uploadedFile['tmp_name']);
			}

		}

	}

}






