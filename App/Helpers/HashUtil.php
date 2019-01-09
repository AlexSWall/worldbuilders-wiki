<?php

namespace App\Helpers;

class HashUtil
{
	protected $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function hashPassword($password)
	{
		return password_hash(
			$password,
			$this->config['password_hash_algorithm'], 
			['cost' => $this->config['cost']]
		);
	}

	public function checkPassword($password, $hash)
	{
		return password_verify($password, $hash);
	}

	public function hash($input)
	{
		return hash($this->config['standard_hash_algorithm'], $input);
	}

	public function checkHash($known, $new)
	{
		return hash_equals($known, $new);
	}
}