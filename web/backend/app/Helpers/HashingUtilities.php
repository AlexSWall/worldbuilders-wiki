<?php declare( strict_types = 1 );

namespace App\Helpers;

class HashingUtilities
{
	protected array $config;

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function hashPassword(string $password): string
	{
		return password_hash(
			$password,
			$this->config['password_hash_algorithm'], 
			['cost' => $this->config['cost']]
		);
	}

	public function checkPassword(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}

	public function hash(string $input): string
	{
		return hash($this->config['standard_hash_algorithm'], $input);
	}

	public function checkHash(string $known, string $new): bool
	{
		return hash_equals($known, $new);
	}
}
