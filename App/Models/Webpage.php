<?php

namespace App\Models;

use App\WikitextConversion\WikitextConverter;

use App\Models\SpecialisedQueries\WebpagePermissionBlockQueries;

use App\Permissions\PermissionsUtilities;
use App\Permissions\WebpagePermissionBlock;

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

	private $webpagePermissionBlocks;
	

	/* == Creators & Retrievers == */

	public static function retrieveWebpageById($id)
	{
		return self::retrieveModelWithEntries(['WebpageId' => $id]);
	}

	public static function retrieveWebpageByUrlPath($urlPath)
	{
		return self::retrieveModelWithEntries(['UrlPath' => $urlPath]);
	}


	/* == Getters & Setters == */

	public function getWebpageId()
	{
		return $this->get('WebpageId');
	}

	public function getTitle()
	{
		return $this->get('Title');
	}

	public function getUrlPath()
	{
		return $this->get('UrlPath');
	}

	public function getWikiText()
	{
		return $this->get('WikiText');
	}

	public function getAllHtml()
	{
		return WebpagePermissionBlock::convertBlocksToHtml( $this->getPermissionBlocks() );
	}

	private function getViewableBlocks( $permissionsExpression )
	{
		return PermissionsUtilities::getViewableBlocks( $permissionsExpression, $this->getPermissionBlocks() );
	}

	public function getHtmlForPermissionsExpression( $permissionsExpression )
	{
		$viewableBlocks = $this->getViewableBlocks( $permissionsExpression );
		return WebpagePermissionBlock::convertBlocksToHtml( $viewableBlocks );
	}

	private function setHtml($html)
	{
		$this->set('Html', $html);
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