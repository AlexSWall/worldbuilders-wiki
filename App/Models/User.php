<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $fillable = [
		'name',
		'email',
		'password',
		'active',
		'active_hash',
		'recover_hash',
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
 
	public function setPasswordRecoveryHash($hashedIdentifier)
	{
		$this->update([
			'recover_hash' => $hashedIdentifier
		]);
	}

	public function removePasswordRecoveryHash()
	{
		$this->update([
			'recover_hash' => null
		]);
	}

	public function hasPermission($permission)
	{
		return (bool) $this->permissions->{$permission};
	}

	public function isAdmin()
	{
		return $this->hasPermission('is_admin');
	}

	public function permissions()
	{
		return $this->hasOne('App\Models\UserPermission', 'user_id');
	}
}