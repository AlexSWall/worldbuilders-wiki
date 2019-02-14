<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $fillable = [
		'username',
		'email',
		'password',
		'active',
		'active_hash',
		'recover_hash',
		'remember_identifier',
		'remember_token'
	];

	public static function getUser($identity)
	{
		return User::where(function($query) use ($identity)
				{
					return $query->where('email', $identity)
						->orwhere('username', $identity);
				})
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

	public function hasPermissions($permissions)
	{
		return (bool) $this->permissions->{$permissions};
	}

	public function isAdmin()
	{
		return $this->hasPermissions('is_admin');
	}

	public function permissions()
	{
		return $this->hasOne('App\Models\UserPermissions', 'user_id', 'id');
	}

	public function getPermissions()
	{
		return $this->permissions;
	}

	public function details()
	{
		return $this->hasOne('App\Models\UserDetails', 'user_id', 'id');
	}

	public function getDetails()
	{
		return $this->details;
	}
}