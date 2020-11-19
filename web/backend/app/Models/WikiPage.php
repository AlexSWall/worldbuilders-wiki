<?php

namespace App\Models;

use App\WikitextConversion\WikitextConverter;

use App\Models\SpecialisedQueries\WikiPagePermissionBlockQueries;

use App\Permissions\PermissionsUtilities;
use App\Permissions\WikiPagePermissionBlock;

class WikiPage extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName()
	{
		return 'WikiPages';
	}
	
	protected static function getPrimaryKey()
	{
		return 'WikiPageId';
	}
	
	protected static function getDefaults()
	{
		return [
		];
	}


	/* == Instance Variables == */

	private $wikiPagePermissionBlocks;
	

	/* == Creators & Retrievers == */

	public static function retrieveWikiPageById($id)
	{
		return self::retrieveModelWithEntries(['WikiPageId' => $id]);
	}

	public static function retrieveWikiPageByUrlPath($urlPath)
	{
		return self::retrieveModelWithEntries(['UrlPath' => $urlPath]);
	}


	/* == Getters & Setters == */

	public function getWikiPageId()
	{
		return $this->get('WikiPageId');
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
		return WikiPagePermissionBlock::convertBlocksToHtml( $this->getPermissionBlocks() );
	}

	private function getViewableBlocks( $permissionsExpression )
	{
		return PermissionsUtilities::getViewableBlocks( $permissionsExpression, $this->getPermissionBlocks() );
	}

	public function getHtmlForPermissionsExpression( $permissionsExpression )
	{
		$viewableBlocks = $this->getViewableBlocks( $permissionsExpression );
		return WikiPagePermissionBlock::convertBlocksToHtml( $viewableBlocks );
	}

	private function setHtml($html)
	{
		$this->set('Html', $html);
	}


	/* == WikiPage Permission Blocks == */

	public function getPermissionBlocks()
	{
		if ( !$this->wikiPagePermissionBlocks )
			$this->setWikiPagePermissionBlocks( 
				WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks( $this->getWikiPageId() ),
				false
			);
		return $this->wikiPagePermissionBlocks;
	}

	public function setPermissionBlocks( $wikiPagePermissionBlocks, $setInDatabase )
	{
		$this->wikiPagePermissionBlocks = $wikiPagePermissionBlocks;
		WikiPagePermissionBlockQueries::setPermissionBlocksForWikiPage( $this->getWikiPageId(), $this->getPermissionBlocks() );
	}

	public function renderWikiTextToHtmlBlocks()
	{
		$permissionBlocks = ( new WikitextConverter )->convertWikitextToHtmlBlocks(
			$this->getWikiText(), $this->getUrlPath()
		);
		$this->setPermissionBlocks( $permissionBlocks, true);
	}
}