<?php

declare(strict_types=1);

namespace App\Globals;

class FrontEndParameters
{
	private ?string $baseUrl = null;
	private ?bool $isAuthenticated = null;
	private ?\App\Helpers\HashingUtilities $hashingUtilities = null;
	private ?array $csrfTokens = null;
	private ?\App\Models\User $userData = null;
	private ?bool $hasRememberMeCookie = null;

	public function _setter( string $field, mixed $value ): void
	{
		if ( $this->$field !== null )
		{
			throw new SettingDefinedGlobalException( $field, $this->$field, $value );
		}

		$this->$field = $value;
	}

	public function _getter( string $field ): mixed
	{
		return $this->{$field};
	}
}

class SettingDefinedGlobalException extends \Exception
{
	public function __construct( string $fieldName, mixed $oldValue, mixed $newValue )
	{
		$message = 'Attempted to set global variable ' . $fieldName
			. ' with old value of ' . $oldValue . ' to new value ' . $newValue . '.';
		parent::__construct( $message, 0, null );
	}
}
