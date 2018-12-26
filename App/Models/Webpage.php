<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webpage extends Model
{
	protected $fillable = [
		'page_name',
		'webpage',
		'is_admin_only',
		'is_global'
	];
}