<?php

declare(strict_types=1);

namespace App\Models;

use App\WikitextConversion\WikitextConverter;

use App\Models\SpecialisedQueries\WikiPagePermissionBlockQueries;

use App\Permissions\PermissionsUtilities;
use App\Permissions\WikiPagePermissionBlock;

use App\Utilities\ArrayBasedSet;

class WikiPage extends DatabaseEncapsulator
{
	public static \App\Logging\Logger $logger;

	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'WikiPages';
	}

	protected static function getPrimaryKey(): string
	{
		return 'WikiPageId';
	}

	protected static function getDefaults(): array
	{
		return [
			'Title' => 'Page Title',
			'WikiText' => 'Content goes here...',
			'Html' => ''
		];
	}


	/* == Instance Variables == */

	private ?array $wikiPagePermissionBlocks = null;


	/* == Creators, Retrievers & Deleter == */

	public static function createWikiPage( string $path, string $title ): ?WikiPage
	{
		self::$logger->info( 'Creating WikiPage with title \'' . $title . '\' and path \'' . $path );

		return self::createModelWithEntries( [
			'Title' => $title,
			'UrlPath' => $path,
		] );
	}

	public static function retrieveWikiPageById( int $id ): ?WikiPage
	{
		return self::retrieveModelWithEntries( ['WikiPageId' => $id] );
	}

	public static function retrieveWikiPageByUrlPath( string $urlPath ): ?WikiPage
	{
		return self::retrieveModelWithEntries( ['UrlPath' => $urlPath] );
	}

	public function delete(): void
	{
		parent::delete();
	}

	/* == Getters & Setters == */

	public function getWikiPageId(): int
	{
		return $this->get( 'WikiPageId' );
	}

	public function getTitle(): string
	{
		return $this->get( 'Title' );
	}

	public function getUrlPath(): string
	{
		return $this->get( 'UrlPath' );
	}

	public function getWikiText(): string
	{
		return $this->get( 'WikiText' );
	}

	public function getAllHtml(): string
	{
		return WikiPagePermissionBlock::convertBlocksToHtml( $this->getPermissionBlocks() );
	}

	/**
	 * Retrieves the viewable HTML for a WikiPage for the supplied list of
	 * permissions
	 *
	 * @param permissions The list of permissions the character has, used to
	 *    determine which permission blocks are viewable.
	 *	@return A string containing the HTML which contains the viewable wikitext
	 *	   content.
	 */
	public function getHtmlForPermissions( ?ArrayBasedSet $permissions ): string
	{
		// Get array of WikiPagePermissionBlocks
		self::$logger->info( 'Getting viewable blocks' );
		$viewableBlocks = PermissionsUtilities::getViewableBlocks( $permissions, $this->getPermissionBlocks() );

		// Convert them to HTML
		self::$logger->info( 'Converting them to HTML' );
		return WikiPagePermissionBlock::convertBlocksToHtml( $viewableBlocks );
	}

	public function updateWikiPage( string $title, string $wikiText, array $permissionBlocks = null, ?string $html = null )
	{
		if ( $permissionBlocks === null || $html == null )
		{
			$converter = new WikitextConverter( $wikiText );
			$permissionBlocks = $converter->getHtmlBlocks();
			$html = $converter->getHtml();
		}

		$this->set( 'Title', $title );
		$this->set( 'WikiText', $wikiText );
		$this->set( 'Html', $html );
		$this->setPermissionBlocks( $permissionBlocks );
	}

	/* == WikiPage Permission Blocks == */

	// To avoid a separate Modal for permission blocks, the required
	// functionality has been included here.
	// The `getViewableBlocks` and `getHtmlForPermissionsExpression` above can
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
	private function getPermissionBlocks(): array
	{
		// Check whether cache private member variable is already populated.
		if ( !$this->wikiPagePermissionBlocks ) {
			// If not, populate it.
			$this->setPermissionBlocks(
				WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks( $this->getWikiPageId() )
			);
		}

		// Return it
		return $this->wikiPagePermissionBlocks;
	}

	/**
	 * Private member function for setting the local permission blocks cache
	 * variable via a specialised permission block query.
	 *
	 * @param wikiPagePermissionBlocks An array of `WikiPagePermissionBlock`s.
	 */
	private function setPermissionBlocks( array $wikiPagePermissionBlocks ): void
	{
		// Update cache private member variable.
		$this->wikiPagePermissionBlocks = $wikiPagePermissionBlocks;

		// Update the database.
		WikiPagePermissionBlockQueries::setPermissionBlocksForWikiPage( $this->getWikiPageId(), $wikiPagePermissionBlocks );
	}
}
