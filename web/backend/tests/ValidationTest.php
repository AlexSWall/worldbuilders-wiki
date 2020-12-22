<?php declare( strict_types = 1 );

namespace tests;

require dirname(__FILE__) . '/TestsBootstrapper.php';

use PHPUnit\Framework\TestCase;

use App\Models\User;
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
			'username1' => [ Rules::usernameAvailableRules(), 'john' ],
			'username2' => [ Rules::usernameAvailableRules(), 'johnsmith123' ],
			'username3' => [ Rules::usernameAvailableRules(), '1234' ]
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'username4' => [ Rules::usernameAvailableRules(), '' ],
			'username5' => [ Rules::usernameAvailableRules(), 'jon' ],
			'username6' => [ Rules::usernameAvailableRules(), 'johnsmithjohnsmithjohnsmithjohnsmith' ],
			'username7' => [ Rules::usernameAvailableRules(), 'john smith' ],
		]);

		$this->assertSame(['username4', 'username5', 'username6', 'username7'], array_keys($errors));
	}

	public function testEmailValidation() : void
	{
		$errors = Validator::validate([
			'email1' => [ Rules::emailAvailableRules(), 'john@smith.com' ],
			'email2' => [ Rules::emailAvailableRules(), 'Foo_Bar@gmail.com' ],
			'email3' => [ Rules::emailAvailableRules(), 'bar123@live.co.uk' ],
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'email4' => [ Rules::emailAvailableRules(), '' ],
			'email5' => [ Rules::emailAvailableRules(), 'john.com' ],
			'email6' => [ Rules::emailAvailableRules(), 'joh n@sm ith.com' ],
		]);

		$this->assertSame(['email4', 'email5', 'email6'], array_keys($errors));
	}

	public function testPasswordValidation() : void
	{
		$errors = Validator::validate([
			'password1' => [ Rules::passwordRules(), 'password'  ],
			'password2' => [ Rules::passwordRules(), 'Testing 1 ! ; "'  ],
			'password3' => [ Rules::passwordRules(), 'password123@!'  ]
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'password4' => [ Rules::passwordRules(), ''  ],
			'password5' => [ Rules::passwordRules(), 'pass'  ],
			'password6' => [ Rules::passwordRules(), 'passwordpasswordpasswordpasswordpassword'  ]
		]);

		$this->assertSame(['password4', 'password5', 'password6'], array_keys($errors));
	}

	private function addJohnSmithUser() : void
	{
		# Hashes for convenience
		$passwordHash = '$2y$10$f/NOOGcbFDczGIzzGYoJ1ORLJnMHztuV.LbTpTkmxUhQ22eGgXtNK';
		$activationHash = '4ea82b7f719ce4d57595706cb2e65f9a407d34b2c0c2dddd8c017f3b1601477f';

		$user = User::createInactiveUser(
			'johnsmith',
			'john@smith.com',
			$passwordHash,
			$activationHash
		);
	}

	public function testUsernameAvailableValidation() : void
	{
		$this->addJohnSmithUser();

		$errors = Validator::validate([
			'username' => [ [ Rules::usernameAvailable() ], 'johnsmith2'  ],
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'username' => [ [ Rules::usernameAvailable() ], 'johnsmith'  ],
		]);

		$this->assertSame(['username'], array_keys($errors), 'Username should not validate as available but does');
	}

	public function testEmailAvailableValidation() : void
	{
		$this->addJohnSmithUser();

		$errors = Validator::validate([
			'email' => [ [ Rules::emailAvailable() ], 'john2@smith.com'  ],
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'email' => [ [ Rules::emailAvailable() ], 'john@smith.com'  ],
		]);

		$this->assertSame(['email'], array_keys($errors), 'Email should not validate as available but does');
	}

	public function testEmailInUseValidation() : void
	{
		$this->addJohnSmithUser();

		$errors = Validator::validate([
			'email' => [ [ Rules::emailInUse() ], 'john@smith.com'  ],
		]);

		$this->assertEmpty($errors, print_r($errors, true) . ' should be empty but isn\'t');

		$errors = Validator::validate([
			'email' => [ [ Rules::emailInUse() ], 'john2@smith.com'  ],
		]);

		$this->assertSame(['email'], array_keys($errors), 'Email should not be in use but validation suggests it is');
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
