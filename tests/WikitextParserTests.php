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

	/* == Headers == */

	public function testBasicHeaderConversion(): void
	{
		$this->wikitextConversionTester('==Header==', '<h2>Header</h2>');
	}

	public function testBasicHeaderConversion2(): void
	{
		$this->wikitextConversionTester('==  Header ===', '<h2>Header</h2>');
	}

	/* == Paragraphs == */

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

	/* == Bold & Italics == */

	public function testBasicBoldConversion(): void
	{
		$this->wikitextConversionTester("'''Testing'''", '<p><b>Testing</b></p>');
	}

	public function testBasicBoldConversion2(): void
	{
		$this->wikitextConversionTester("a''' bc '''d", '<p>a<b> bc </b>d</p>');
	}

	public function testBasicItalicsConversion(): void
	{
		$this->wikitextConversionTester("''Testing''", '<p><i>Testing</i></p>');
	}

	public function testBasicItalicsConversion2(): void
	{
		$this->wikitextConversionTester("a'' bc ''d", '<p>a<i> bc </i>d</p>');
	}

	public function testBoldInsideItalicsConversion(): void
	{
		$this->wikitextConversionTester("Some ''italics with '''bold''' inside''.", '<p>Some <i>italics with <b>bold</b> inside</i>.</p>');
	}

	public function testBoldInsideItalicsConversion2(): void
	{
		$this->wikitextConversionTester("Some '''bold with ''italics'' inside'''.", '<p>Some <b>bold with <i>italics</i> inside</b>.</p>');
	}

	/* == Wikilinks == */

	public function testSimpleWikilinkConversion(): void
	{
		$this->wikitextConversionTester("[[Place]]", "<p><a href='/#Place'>Place</a></p>");
	}

	public function testSimpleWikilinkConversion2(): void
	{
		$this->wikitextConversionTester("[[  Place with Spaces   ]]", "<p><a href='/#Place_With_Spaces'>Place with Spaces</a></p>");
	}

	public function testTextWikilinkConversion(): void
	{
		$this->wikitextConversionTester("[[Place|text]]", "<p><a href='/#Place'>text</a></p>");
	}

	public function testTextWikilinkConversion2(): void
	{
		$this->wikitextConversionTester("[[ Place link  |  some text ]]  ", "<p><a href='/#Place_Link'>some text</a></p>");
	}

	/* == Headers with Paragraphs == */

	public function testHeaderBetweenParagraphsConversion(): void
	{
		$this->wikitextConversionTester(
			"First paragraph.\n==Heading==\nSecond paragraph.",
			"<p>First paragraph.</p>\n<h2>Heading</h2>\n<p>Second paragraph.</p>"
		);
	}

	/* == Complex Wikitext Rules == */

	public function testComplexWikitextConversion(): void
	{
		$this->wikitextConversionTester(
			" Intro  \n== Heading ===\n[[P2L1]]\n[[ P2 | L2 ]]\n== Heading  2==\n\n\n '''P''3L''1''' \n ====Sub Heading===\nend",
			"<p>Intro</p>\n<h2>Heading</h2>\n<p><a href='/#P2L1'>P2L1</a>\n<a href='/#P2'>L2</a></p>\n<h2>Heading  2</h2>\n\n\n<p><b>P<i>3L</i>1</b></p>\n<h3>Sub Heading</h3>\n<p>end</p>"
		);
	}
}