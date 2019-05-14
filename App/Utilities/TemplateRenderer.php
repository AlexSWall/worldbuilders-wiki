<?php

namespace App\Utilities;

use App\Models\Webpage;

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

		$patternAndReplacementPairs = [

			/* == Images == */
			/* [[Image:"url"|width,height]]  ->  <img src="url" width="width" height="height"> */
			'/\[\[Image:&quot;([^\]\[\|]+)&quot; *\| *(\d+), *(\d+)\]\]/' => function($matches)
				{
					[$str, $location, $width, $height] = $matches;
					return "<img src=\"/resources/images/{$location}\" width=\"{$width}\" height=\"{$height}\">";
				},

			/* == Links == */
			/* [[text]]  ->  <a href="/#text_with_underscores\">text</a> */
			'/\[\[([^\]\[\|]+)\]\]/' => function($matches)
				{
					$targetURL = preg_replace('/\s+/', '_', $matches[1]);
					$targetText = $matches[1];
					return "<a href=\"/#{$targetURL}\">{$targetText}</a>";
				},

			/* [[target|text]]  ->  <a href="/#target_with_underscores\">text</a> */
			'/\[\[([^\]\[\|]+)\|([^\]\[\|]+)\]\]/' => function($matches)
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
					[$str, $start, $middle, $end] = $matches;
					return "{$start}<p>{$middle}</p>{$end}";
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
