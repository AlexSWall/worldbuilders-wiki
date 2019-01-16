<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'users'; /* Not needed due to magic converting User to users. */

	protected $fillable = [
		'name',
		'email',
		'password',
		'active',
		'active_hash',
		'remember_identifier',
		'remember_token'
	];

	public static function getUser($email)
	{
		return User::where('email', $email)
			->where('active', true)
			->first();
	}

	public function setPassword($password)
	{
		$this->update([
			'password' => password_hash($password, PASSWORD_DEFAULT)
		]);
	}

	public function activateAccount()
	{
		$this->update([
			'active' => true,
			'active_hash' => null
		]);
	}

	public function updateRememberCredentials($identifier, $token)
	{
		$this->update([
			'remember_identifier' => $identifier,
			'remember_token' => $token
		]);
	}

	public function removeRememberCredentials()
	{
		$this->updateRememberCredentials(null, null);
	}
}