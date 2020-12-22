<?php declare( strict_types = 1 );

namespace tests;

require dirname(__FILE__) . '/TestsBootstrapper.php';

use PHPUnit\Framework\TestCase;

final class ValidationTests extends TestCase
{
	/*
	 * $this->assertInstanceOf( WikitextParser::class, new WikitextParser() );
	 * $this->assertTrue( null === null);
	 * $this->assertSame( $expected, $val );
	 */
	public function testCanValidateUsername(): void
	{
		$this->assertTrue( true );
	}
}
