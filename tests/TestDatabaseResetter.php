<?php

namespace tests;

use Illuminate\Database\Capsule\Manager as DB;

class TestDatabaseResetter
{
	private $container;

	public function __construct( $container )
	{
		$this->container = $container;
	}

	public function reset()
	{
		assert( DB::getDatabaseName() === 'website_tests' );
		$this->wipe();
		$this->populate();
	}

	public function wipe()
	{
		$tables = [
			'CharacterDetails',
			'CharacterPermissionRelations',
			'Characters',
			'UserDetails',
			'UserPermissions',
			'Users',
			'WebpagePermissionBlocks',
			'Webpages',
			'WikiPermissions'
		];

		foreach ( $tables as $table )
			DB::table($table)->delete();
	}

	public function populate()
	{
		DB::table('Users')->insert([
			'UserId' => 1,
			'Username' => 'User',
			'Email' => 'alexwalldnd@gmail.com',
			'Password' => $this->container->HashUtils->hashPassword('password'),
			'Active' => 1
		]);

		DB::table('UserDetails')->insert([
			'Id' => 1,
			'UserId' => 1,
			'PreferredName' => 'User Name'
		]);

		DB::table('UserPermissions')->insert([
			'Id' => 1,
			'UserId' => 1,
			'IsAdmin' => 0
		]);

		DB::table('Characters')->insert([
			'CharacterId' => 1,
			'UserId' => 1,
		]);

		DB::table('CharacterDetails')->insert([
			'Id' => 1,
			'CharacterId' => 1,
			'Fullname' => 'Character Name'
		]);

		DB::table('WikiPermissions')->insert([
			[
				'PermissionId' => 1,
				'PermissionName' => 'permission_one',
				'PermissionDescription' => 'This is permission one.'
			],
			[
				'PermissionId' => 2,
				'PermissionName' => 'permission_two',
				'PermissionDescription' => 'This is permission two.'
			]
		]);

		DB::table('CharacterPermissionRelations')->insert([
			'Id' => 1,
			'CharacterId' => 1,
			'PermissionId' => 1
		]);

		DB::table('Webpages')->insert([
			'WebpageId' => 1,
			'WebpageName' => 'Page_One',
			'WebpageTitle' => 'Page One',
			'WebpageTemplate' => 'First paragraph.\n\n== Heading 1 ==\n\nSecond paragraph.',
			'WebpageHTML' => '<p>First paragraph.</p>\n\n<h2>Heading 1</h2>\n\n<p>Second paragraph.</p>',
			'isAdminOnly' => 0,
			'isGlobal' => 0
		]);

		DB::table('WebpagePermissionBlocks')->insert([
			'BlockId' => 1,
			'WebpageId' => 1,
			'PermissionsExpression' => '',
			'BlockPosition' => '0',
			'HTML' => '<p>First paragraph.</p>\n\n<h2>Heading 1</h2>\n\n<p>Second paragraph.</p>'
		]);
	}
}
