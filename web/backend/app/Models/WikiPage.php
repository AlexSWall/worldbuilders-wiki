<?php

namespace App\Models;

use App\WikitextConversion\WikitextConverter;

use App\Models\SpecialisedQueries\WikiPagePermissionBlockQueries;

use App\Permissions\PermissionsUtilities;
use App\Permissions\WikiPagePermissionBlock;

class WikiPage extends DatabaseEncapsulator
{
	static $logger;

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
			'Title' => 'Page Title',
			'WikiText' => 'Content goes here...',
			'Html' => ''
		];
	}


	/* == Instance Variables == */

	private $wikiPagePermissionBlocks;
	

	/* == Creators, Retrievers & Deleter == */

	public static function createWikiPage($path, $title)
	{
		self::$logger->addInfo('Creating WikiPage with title \'' . $title . '\' and path \'' . $path);

		return self::createModelWithEntries([
			'Title' => $title,
			'UrlPath' => $path,
		]);
	}

	public static function retrieveWikiPageById($id)
	{
		return self::retrieveModelWithEntries(['WikiPageId' => $id]);
	}

	public static function retrieveWikiPageByUrlPath($urlPath)
	{
		return self::retrieveModelWithEntries(['UrlPath' => $urlPath]);
	}

	public function delete()
	{
		parent::delete();
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

	/**
	 * Retrieves an array of WikiPagePermissionBlocks that are viewable given the
	 * supplied permissions expression.
	 *
	 * To retrieve the viewable _HTML_ for a given permissionsExpression, use the
	 * `getHtmlForPermissionsExpression` function.
	 *
	 * @param permissionsExpression The permissions expression string to use to
	 *		determine which permission blocks are viewable.
	 *	@return An array of Wikitext permission blocks.
	 */
	private function getViewableBlocks( $permissionsExpression )
	{
		return PermissionsUtilities::getViewableBlocks( $permissionsExpression, $this->getPermissionBlocks() );
	}

	/**
	 * Retrieves the viewable HTML for a WikiPage for the supplied permissions
	 * expression.
	 *
	 * This function is purely a convenience function to avoid needing to call
	 * `getViewableBlocks` and `convertBlocksToHtml` manually.
	 *
	 * @param permissionsExpression The permissions expression string to use to
	 *		determine which permission blocks are viewable.
	 *	@return A string containing the HTML which contains the viewable wikitext
	 *	content.
	 */
	public function getHtmlForPermissionsExpression( $permissionsExpression )
	{
		// Get array of WikiPagePermissionBlocks
		self::$logger->addInfo('Getting viewable blocks');
		$viewableBlocks = $this->getViewableBlocks( $permissionsExpression );

		// Convert them to HTML.
		self::$logger->addInfo('Converting them to HTML');
		return WikiPagePermissionBlock::convertBlocksToHtml( $viewableBlocks );
	}

	public function updateWikiPage($title, $wikiText, $permissionBlocks = null, $html = null)
	{
		if ( $permissionBlocks === null || $html == null )
		{
			$converter = new WikitextConverter( $wikiText );
			$permissionBlocks = $converter->getHtmlBlocks();
			$html = $converter->getHtml();
		}

		$this->set('Title', $title);
		$this->set('WikiText', $wikiText);
		$this->set('Html', $html);
		$this->setPermissionBlocks($permissionBlocks);
	}

	/* == WikiPage Permission Blocks == */

	// To avoid a separate Modal for permission blocks, the required
	// functionality has been included here.
	// The `getViewableBlocks` and `getHtmlForPermissionsExpression` above can
	//
	// be used to get the actual wikitext blocks and the HTML for the wikitext
	// blocks that are viewable for a given permissions expression.

	/* -- Private Member Functions -- */
	/**
	 * Gets the `WikiPagePermissionBlock`s for this WikiPage from the database.
	 *
	 * This is only called within the class.
	 *
	 * @return An array of `WikiPagePermissionBlock`s.
	 */
	private function getPermissionBlocks()
	{
		// Check whether cache private member variable is already populated.
		if ( !$this->wikiPagePermissionBlocks )
			// If not, populate it.
			$this->setPermissionBlocks( 
				WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks( $this->getWikiPageId() )
			);

		// Return it
		return $this->wikiPagePermissionBlocks;
	}

	/**
	 * Private member function for setting the local permission blocks cache
	 * variable via a specialised permission block query.
	 *
	 * @param wikiPagePermissionBlocks An array of `WikiPagePermissionBlock`s.
	 */
	private function setPermissionBlocks( $wikiPagePermissionBlocks )
	{
		// Update cache private member variable.
		$this->wikiPagePermissionBlocks = $wikiPagePermissionBlocks;
		// Update the database.
		WikiPagePermissionBlockQueries::setPermissionBlocksForWikiPage( $this->getWikiPageId(), $wikiPagePermissionBlocks );
	}
}
