<?php

namespace App\Utilities;

/** === Regular Expression Syntax Rules ===
 *
 * For testing: https://regex101.com/
 *
 * The following should be escaped if one is trying to match the character:
 *     \ ^ . $ | ( ) [ ] * + ? { } ,
 * 
 * == Special Character Definitions ==
 *   \ Quote the next metacharacter
 *   ^ Match the beginning of the line
 *   . Match any character (except newline)
 *   $ Match the end of the line (or before newline at the end)
 *   | Alternation
 *   () Grouping
 *   [] Character class
 *   * Match 0 or more times
 *   + Match 1 or more times
 *   ? Match 1 or 0 times
 *   {n} Match exactly n times
 *   {n,} Match at least n times
 *   {n,m} Match at least n but not more than m times
 *
 * == More Special Character Stuff ==
 *   \t tab (HT, TAB)
 *   \n newline (LF, NL)
 *   \r return (CR)
 *   \f form feed (FF)
 *   \a alarm (bell) (BEL)
 *   \e escape (think troff) (ESC)
 *   \033 octal char (think of a PDP-11)
 *   \x1B hex char
 *   \c[ control char
 *   \l lowercase next char (think vi)
 *   \u uppercase next char (think vi)
 *   \L lowercase till \E (think vi)
 *   \U uppercase till \E (think vi)
 *   \E end case modification (think vi)
 *   \Q quote (disable) pattern metacharacters till \E
 *
 * == Even More Special Characters ==
 *   \w Match a "word" character (alphanumeric plus "_")
 *   \W Match a non-word character
 *   \s Match a whitespace character
 *   \S Match a non-whitespace character
 *   \d Match a digit character
 *   \D Match a non-digit character
 *   \b Match a word boundary
 *   \B Match a non-(word boundary)
 *   \A Match only at beginning of string
 *   \Z Match only at end of string, or before newline at the end
 *   \z Match only at end of string
 *   \G Match only where previous m//g left off (works only with /g)
 */

class TemplateRenderer
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public static function renderTemplate($pageName, $templateContent)
	{
		$workingContent = htmlspecialchars($templateContent, ENT_QUOTES, 'UTF-8');
		$workingContent = str_replace("\r", '', $workingContent);

		//var_dump($workingContent);
		$patternAndReplacementPairs = [

			/* == Images == */
			/* [[Image:"url"|width,height]]  ->  <img src="url" width="width" height="height"> */
			'/\[\[ *Image: *&quot;([^\/\]\[\|]+)&quot; *\| *(\d+), *(\d+) *\]\]/' => function($matches)
				{
					[, $location, $width, $height] = $matches;
					return "<img src=\"/images/{$location}\" width=\"{$width}\" height=\"{$height}\">";
				},

			/* == Links == */
			/* [[text]]  ->  <a href="/#text_with_underscores\">text</a> */
			'/\[\[ *([^\]\[\|]+) *\]\]/' => function($matches)
				{
					$targetURL = preg_replace('/\s+/', '_', $matches[1]);
					$targetText = $matches[1];
					return "<a href=\"/#{$targetURL}\">{$targetText}</a>";
				},

			/* [[target|text]]  ->  <a href="/#target_with_underscores\">text</a> */
			'/\[\[ *([^\]\[\|]+)\|([^\]\[\|]+) *\]\]/' => function($matches)
				{
					$targetURL = preg_replace('/\s+/', '_', $matches[1]);
					$targetText = $matches[2];
					return "<a href=\"/#{$targetURL}\">{$targetText}</a>";
				},

			/* == Sections == */
			/* == Section title ==  ->  <h2>Section title</h2>*/
			'/(==+) *([^=]+?) *(==+)/' => function($matches)
				{
					if ( strlen($matches[1]) !== strlen($matches[3]) )
						return $matches[0];

					$secNum = strlen($matches[1]);
					if ($secNum > 6)
						return $matches[0];
					$heading = $matches[2];

					return "<h{$secNum}>{$heading}</h{$secNum}>";
				},

			/* == Lists == */
			'/(((\n|^)[^-\n][^\n]*|^)\s*)(-[^\n]*(\n|$)([ \t]*((\n|$)|(-[^\n]*(\n|$))))*)(\s*($|[^-]))/' => function($matches)
				{
					return "{$matches[1]}<ul>\n{$matches[4]}\n</ul>{$matches[11]}";
				},

			'/\n[ \t]*-[ \t]*([^\n]*)/' => function($matches)
				{
					return "\n<li>{$matches[1]}</li>";
				},

			/* == Paragraphs == */
			/* The beginning or >=2 new lines until the end or two new lines, with anything between, matched lazily. */
			'/(^\n*|\n\n+)([^<>\[\]=][\S\s]+?[^<>\[\]=])(?=(\n*$|\n\n))/' => function($matches)
				{
					[, $start, $middle,] = $matches;
					return "{$start}<p>{$middle}</p>";
				},

			/* == Bold & Italics == */
			/* ''text''  ->  <i>text</i> */
			"/[^']''([^']|[^'][\S\s]*?[^'])''[^']/" => function($matches)
				{
					return "<i>{$matches[1]}</i>";
				},

			/* '''text'''  ->  <b>text</b> */
			"/[^']'''([^']|[^'][\S\s]*?[^'])'''[^']/" => function($matches)
				{
					return "<b>{$matches[1]}</b>";
				},

			/* '''''text'''''  ->  <b><i>text</i></b> */
			"/[^']'''''([^']|[^'][\S\s]*?[^'])'''''[^']/" => function($matches)
				{
					return "<b><i>{$matches[1]}</i></b>";
				}
		];

		$workingContent = preg_replace_callback_array(
			$patternAndReplacementPairs,
			$workingContent
		);

		if ( false )
		{
			/* Add only if four headings exist. */
			if ( stripos($workingContent, '[[Table of Contents]]' ) !== false )
			{
				[$workingContent, $tableOfContents] 
						= TemplateRenderer::addTableOfContents('/#' . $pageName, $workingContent);

				$workingContent = preg_replace(
					'/\[\[Table of Contents\]\]/i',
					"<div id=\"toc\">{$tableOfContents}</div>",
					$workingContent
				);
			}
		}

		return $workingContent;
	}

	public static function addTableOfContents($url, $content)
	{
		$toc = "<h2>Table of Contents</h2>";
		$prevLevel = 1;
		$isEmpty = true;

		$generatedContent = preg_replace_callback(
			'/<h([2-6])>([^<]+)<\/h([2-6])>/i',
			function ($match) use ($url, &$toc, &$prevLevel, &$isEmpty)
			{
				[$str, $openLevel, $titleText, $closeLevel] = $match;
				$openLevel = intval($openLevel);
				$closeLevel = intval($closeLevel);

				if ($openLevel != $closeLevel)
					return $str;

				$isEmpty = false;

				if ($openLevel > $prevLevel)
					$toc .= str_repeat("<ul>", $openLevel - $prevLevel);
				else if ($openLevel < $prevLevel)
					$toc .= str_repeat("</ul>", $prevLevel - $openLevel);

				$prevLevel = $openLevel;

				$anchor = preg_replace('/ /', '_', $titleText);
				$toc .= "<li><a href=\"{$url}#{$anchor}\">{$titleText}</a></li>";

				return "<h{$openLevel}><a class=\"anchor\" id=\"{$anchor}\">{$titleText}</a></h{$closeLevel}>";
			},
			$content
		);

		if (!$isEmpty)
		{
			if ($prevLevel > 0) 
				$toc .= str_repeat("</ul>", $prevLevel);
		}
		else
			$toc = "";

		return [$generatedContent, $toc];
	}
}
