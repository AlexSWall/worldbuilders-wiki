<?php

namespace App\Globals;

use Exception;

class FrontEndParameters
{
	private $baseUrl;
	private $isAuthenticated;
	private $csrfTokens;
	private $previousParameters;
	private $errors;
	private $userData;
	private $flash;

	public function _setter($field, $value)
	{
		if ( !is_null($this->$field) )
			throw new SettingDefinedGlobalException($field, $this->$field, $value);
		$this->$field = $value;
	}

	public function _getter($field)
	{
		return $this->{$field};
	}
}

class SettingDefinedGlobalException extends Exception
{
    public function __construct($fieldName, $oldValue, $newValue) {
    	$message = 'Attempted to set global variable ' . $fieldName
    		. ' with old value of ' . $oldValue . ' to new value ' . $newValue . '.';
        parent::__construct($message, 0, null);
    }
}
