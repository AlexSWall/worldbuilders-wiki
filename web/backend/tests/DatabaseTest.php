<?php declare( strict_types = 1 );

namespace tests;

$app = require dirname(__FILE__) . '/TestsBootstrapper.php';

use PHPUnit\Framework\TestCase;

use App\Utilities\ArrayBasedSet;
use App\Models\SpecialisedQueries\CharacterPermissionsQueries;
use App\Models\SpecialisedQueries\WikiPagePermissionBlockQueries;
use App\Permissions\WikiPagePermissionBlock;

final class DatabaseTests extends TestCase
{
	protected $isInitialised;
	protected $container;
	protected $databaseResetter;

	protected function setUp(): void
	{
		if( ! $this->isInitialised )
		{
			global $app;
			$this->container = $app->getContainer();
			$this->databaseResetter = new TestDatabaseResetter($this->container);
		}
		
		$this->databaseResetter->reset();
	}


	/* == Character Permissions == */

	public function testCanGetCharacterPermissions(): void
	{
		$permissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertSame( count($permissions), 1 );
	}

	public function testCanAddCharacterPermissions(): void
	{
		CharacterPermissionsQueries::addCharacterPermissions(1, ['permission_two']);
		$permissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertSame( count($permissions), 2 );
	}

	public function testCanRemoveCharacterPermissions(): void
	{
		$permissions = new ArrayBasedSet(['permission_one']);
		CharacterPermissionsQueries::removeCharacterPermissions(1, $permissions);
		$newPermissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertSame( count($newPermissions), 0 );
	}


	/* == WikiPage Permission Blocks == */

	public function testCanGetWikiPagePermissionBlocks(): void
	{
		$wikiPagePermissionBlocks = WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks(1);
		$this->assertSame( count($wikiPagePermissionBlocks), 1 );
		$this->assertTrue( method_exists( $wikiPagePermissionBlocks[0], 'getPermissionsExpression' ) );
		$this->assertTrue( method_exists( $wikiPagePermissionBlocks[0], 'getHtml' ) );
	}

	public function testCanClearWikiPagePermissionBlocks(): void
	{
		WikiPagePermissionBlockQueries::clearPermissionBlocksForWikiPage(1);
		$wikiPagePermissionBlocks = WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks(1);
		$this->assertSame( count($wikiPagePermissionBlocks), 0 );
	}

	public function testCanSetWikiPagePermissionBlocks(): void
	{
		$blocksToSet = [
			new WikiPagePermissionBlock('', '<p>Some text.</p>\n\n'),
			new WikiPagePermissionBlock('is_alive', '<h2>Header</h2>\n\n<p>Text.</p>')
		];
		WikiPagePermissionBlockQueries::setPermissionBlocksForWikiPage( 1, $blocksToSet );
		$newBlocks = WikiPagePermissionBlockQueries::getWikiPagePermissionBlocks(1);
		$this->assertSame( count($newBlocks), 2 );
		$this->assertTrue( method_exists( $newBlocks[1], 'getPermissionsExpression' ) );
		$this->assertTrue( method_exists( $newBlocks[1], 'getHtml' ) );
	}
}
