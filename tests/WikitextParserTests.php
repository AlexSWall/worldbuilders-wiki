<?php declare( strict_types = 1 );

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
		$this->assertSame( $expected, ( new WikitextConverter() )->convertWikitextToHTML($wikitextToConvert) );
	}

	public function testBasicHeaderConversion(): void
	{
		$this->wikitextConversionTester('==Header==', '<h2>Header</h2>');
	}

	public function testBasicHeaderConversion2(): void
	{
		$this->wikitextConversionTester('==  Header ===', '<h2>Header</h2>');
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

	public function testHeaderBetweenParagraphs(): void
	{
		$this->wikitextConversionTester(
			"First paragraph.\n==Heading==\nSecond paragraph.",
			"<p>First paragraph.</p>\n<h2>Heading</h2>\n<p>Second paragraph.</p>"
		);
	}

	public function testComplexWikitextConversion(): void
	{
		$this->wikitextConversionTester(
			" Intro  \n== Heading ===\nP2L1\nP2L2\n== Heading  2==\n\n\n '''P''3L''1''' \n ====Sub Heading===\nend",
			"<p>Intro</p>\n<h2>Heading</h2>\n<p>P2L1\nP2L2</p>\n<h2>Heading  2</h2>\n\n\n<p><b>P<i>3L</i>1</b></p>\n<h3>Sub Heading</h3>\n<p>end</p>"
		);
	}
}