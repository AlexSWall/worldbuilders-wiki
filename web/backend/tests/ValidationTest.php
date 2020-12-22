<?php declare( strict_types = 1 );

namespace tests;

require dirname(__FILE__) . '/TestsBootstrapper.php';

use PHPUnit\Framework\TestCase;

use App\Validation\Rules;
use App\Validation\Validator;

final class ValidationTests extends TestCase
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

	/*
	 * $this->assertInstanceOf( WikitextParser::class, new WikitextParser() );
	 * $this->assertTrue( null === null);
	 * $this->assertSame( $expected, $val );
	 */

	public function testUsernameValidation() : void
	{
		$errors = Validator::validate([
			'username1'       => [ Rules::usernameAvailableRules(), 'john' ],
			'username2'       => [ Rules::usernameAvailableRules(), 'johnsmith123' ],
			'username3'       => [ Rules::usernameAvailableRules(), '1234' ]
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'username4'       => [ Rules::usernameAvailableRules(), '' ],
			'username5'       => [ Rules::usernameAvailableRules(), 'jon' ],
			'username6'       => [ Rules::usernameAvailableRules(), 'johnsmithjohnsmithjohnsmithjohnsmith' ],
			'username7'       => [ Rules::usernameAvailableRules(), 'john smith' ],
		]);

		$this->assertSame(array_keys($errors), ['username4', 'username5', 'username6', 'username7']);
	}

	public function testEmailValidation() : void
	{
		$errors = Validator::validate([
			'email1'          => [ Rules::emailAvailableRules(),    'john@smith.com' ],
			'email2'          => [ Rules::emailAvailableRules(),    'john@smith.com' ],
			'email3'          => [ Rules::emailAvailableRules(),    'john@smith.com' ],
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'email4'          => [ Rules::emailAvailableRules(),    '' ],
			'email5'          => [ Rules::emailAvailableRules(),    'john.com' ],
			'email6'          => [ Rules::emailAvailableRules(),    'joh n@sm ith.com' ],
		]);

		$this->assertSame(array_keys($errors), ['email4', 'email5', 'email6']);
	}

	public function testPasswordValidation() : void
	{
		$errors = Validator::validate([
			'password1'       => [ Rules::passwordRules(),          'password'  ],
			'password2'       => [ Rules::passwordRules(),          'Testing 1 ! ; "'  ],
			'password3'       => [ Rules::passwordRules(),          'password123@!'  ]
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'password4'       => [ Rules::passwordRules(),          ''  ],
			'password5'       => [ Rules::passwordRules(),          'pass'  ],
			'password6'       => [ Rules::passwordRules(),          'passwordpasswordpasswordpasswordpassword'  ]
		]);

		$this->assertSame(array_keys($errors), ['password4', 'password5', 'password6']);
	}

	public function testCanValidateMany() : void
	{
		$errors = Validator::validate([
			'preferred_name' => [ Rules::preferredNameRules(),     'John Smith'     ],
			'username'       => [ Rules::usernameAvailableRules(), 'johnsmith'      ],
			'email'          => [ Rules::emailAvailableRules(),    'john@smith.com' ],
			'password'       => [ Rules::passwordRules(),          'password123@!'  ]
		]);

		$this->assertSame($errors, []);
	}
}
