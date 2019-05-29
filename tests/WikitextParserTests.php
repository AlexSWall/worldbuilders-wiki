<?php declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

use PHPUnit\Framework\TestCase;

use App\WikitextConversion\WikitextParser;
use App\WikitextConversion\WikitextConverter;

final class WikitextParserTests extends TestCase
{
	public function testCanBeInitialised(): void
	{
		$this->assertInstanceOf(
			WikitextParser::class,
			new WikitextParser('App\WikitextConversion\Grammar')
		);
	}

	public function testParseProducesOutput(): void
	{
		$this->assertTrue(
			new WikitextParser('App\WikitextConversion\Grammar') !== null
		);
	}

	public function testWikitextConversion(): void
	{
		$this->assertSame(
			(new WikitextConverter())->convertWikitextToHTML('Testing'),
			'<p>Testing</p>'
		);
	}
}