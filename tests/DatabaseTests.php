<?php declare( strict_types = 1 );

namespace tests;

$app = require dirname(__FILE__) . '/TestsBootstrapper.php';

use PHPUnit\Framework\TestCase;

use App\Utilities\ArrayBasedSet;
use App\Models\SpecialisedQueries\CharacterPermissionsQueries;

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

	public function testCanGetCharacterPermissions(): void
	{
		$permissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertTrue( count($permissions) === 1 );
	}

	public function testCanAddCharacterPermissions(): void
	{
		CharacterPermissionsQueries::addCharacterPermissions(1, ['permission_two']);
		$permissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertTrue( count($permissions) === 2 );
	}

	public function testCanRemoveCharacterPermissions(): void
	{
		$permissions = new ArrayBasedSet(['permission_one']);
		CharacterPermissionsQueries::removeCharacterPermissions(1, $permissions);
		$newPermissions = CharacterPermissionsQueries::getCharacterPermissions(1);
		$this->assertTrue( count($newPermissions) === 0 );
	}
}