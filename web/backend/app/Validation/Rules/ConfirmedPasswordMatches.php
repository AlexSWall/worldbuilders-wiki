<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ConfirmedPasswordMatches extends AbstractRule
{
	protected $password;

	public function __construct($password)
	{
		$this->password = $password;
	}

	public function validate($confirmation)
	{
		return $confirmation === $this->password;
	}
}