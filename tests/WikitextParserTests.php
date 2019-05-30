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

	private function wikitextConversionTester($wikitextToConvert, $expected): void
	{
		$this->assertSame( $expected,
			( new WikitextConverter() )->convertWikitextToHTML($wikitextToConvert)
		);
	}

	public function testBasicParagraphConversion(): void
	{
		$this->wikitextConversionTester('Testing', '<p>Testing</p>');
	}

	public function testMultipleParagraphsConversion(): void
	{
		$this->wikitextConversionTester(
			"P1 Line1\nP1 Line2\n\nP2 Line1\nP2 Line2\n\nP3 Line3",
			"<p>P1 Line1\nP1 Line2</p>\n\n<p>P2 Line1\nP2 Line2</p>\n\n<p>P3 Line3</p>"
		);
	}
}