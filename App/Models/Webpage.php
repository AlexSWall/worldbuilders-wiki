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
		return 'page_name';
	}
	

	/* == Creators & Retrievers == */

	public static function retrieveWebpageByName($pageName)
	{
		return self::retrieveModelWithEntries(['page_name' => $pageName]);
	}

	
	/* == Getters & Setters == */

	public function getWebpageName()
	{
		return $this->get('page_name');
	}

	public function getWebpageTemplate()
	{
		return $this->get('webpage_template');
	}

	public function getWebpageHTML()
	{
		return $this->get('webpage_html');
	}

	public function renderWebpageTemplateToHTML()
	{
		return $this->set('webpage_html', TemplateRenderer::renderTemplate(
			$this->getWebpageName(), $this->getWebpageTemplate()
		));
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