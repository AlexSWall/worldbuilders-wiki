<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webpage extends Model
{
	protected $fillable = [
		'page_name',
		'webpage_raw',
		'webpage_html',
		'is_admin_only',
		'is_global'
	];

	public static function getWebpage($pageName)
	{
		return Webpage::where('page_name', $pageName)->first();
	}

	public function getWebpageRaw($pageName)
	{
		return $this->webpage_raw;
	}

	public function getWebpageHTML($pageName)
	{
		return $this->webpage_html;
	}

	public function isAdminOnly()
	{
		return $this->is_admin_only;
	}

	public function isGloballyAccessible()
	{
		return $this->is_global;
	}
}