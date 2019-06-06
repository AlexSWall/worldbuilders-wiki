<?php

namespace App\Models;

use App\WikitextConversion\WikitextConverter;

class Webpage extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName()
	{
		return 'Webpages';
	}
	
	protected static function getPrimaryKey()
	{
		return 'WebpageId';
	}
	
	protected static function getDefaults()
	{
		return [
		];
	}


	/* == Instance Variables == */

	private $webpagePermissionBlockModels;
	

	/* == Creators & Retrievers == */

	public static function retrieveWebpageById($id)
	{
		return self::retrieveModelWithEntries(['id' => $id]);
	}

	public static function retrieveWebpageByName($webpageName)
	{
		return self::retrieveModelWithEntries(['WebpageName' => $webpageName]);
	}

	
	/* == Getters & Setters == */

	public function getWebpageId()
	{
		return $this->get('id');
	}

	public function getWebpageName()
	{
		return $this->get('WebpageName');
	}

	public function getWebpageTitle()
	{
		return $this->get('WebpageTitle');
	}

	public function getWebpageTemplate()
	{
		return $this->get('WebpageTemplate');
	}

	public function getWebpageHTML()
	{
		return $this->get('WebpageHTML');
	}

	private function setWebpageHTML($webpageHTML)
	{
		$this->set('WebpageHTML', $webpageHTML);
	}

	public function renderWebpageTemplateToHTML()
	{
		$this->setWebpageHTML( ( new WikitextConverter )->convertWikitextToHtml(
			$this->getWebpageTemplate(), $this->getWebpageName()
		));
	}

	public function isAdminOnly()
	{
		return $this->get('IsAdminOnly');
	}

	public function isGloballyAccessible()
	{
		return $this->get('IsGlobal');
	}

}