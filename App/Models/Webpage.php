<?php

namespace App\Models;

class Webpage extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
		];
	}

	protected static function getTableName()
	{
		return 'webpages';
	}
	
	protected static function getPrimaryKey()
	{
		return 'page_name';
	}
	

	/* == Creators & Retrievers == */

	public static function retrieveWebpageByName($pageName)
	{
		return self::retrieveModelWithEntries(['page_name' => $pageName]);
	}

	
	/* == Getters & Setters == */
	
	public function getWebpageRaw()
	{
		return $this->get('webpage_raw');
	}

	public function getWebpageHTML()
	{
		return $this->get('webpage_html');
	}

	public function isAdminOnly()
	{
		return $this->get('is_admin_only');
	}

	public function isGloballyAccessible()
	{
		return $this->get('is_global');
	}

}