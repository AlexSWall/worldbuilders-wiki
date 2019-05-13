<?php

namespace App\Models;

use App\Utilities\TemplateRenderer;

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
		return 'WebpageName';
	}
	

	/* == Creators & Retrievers == */

	public static function retrieveWebpageByName($webpageName)
	{
		return self::retrieveModelWithEntries(['WebpageName' => $webpageName]);
	}

	
	/* == Getters & Setters == */

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
		$this->setWebpageHTML(TemplateRenderer::renderTemplate(
			$this->getWebpageName(), $this->getWebpageTemplate()
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