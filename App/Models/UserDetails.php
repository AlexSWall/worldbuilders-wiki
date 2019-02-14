<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
	protected $table = 'users_details';

	protected $fillable = [
		'preferred_name',
		'description'
	];

	public static function createUserDetailsArray($details)
	{
		foreach ($details as &$value)
    		if ( empty($value) )
    			$value = null;
		unset($value);

		return array_replace( UserDetails::$defaults, $details );
	}

	public static $defaults = [
		'preferred_name' => null,
		'description' => null
	];
}