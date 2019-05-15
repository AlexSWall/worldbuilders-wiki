<?php

namespace App\Helpers;

class FrontendUtils
{
	public static function constructFrontendParametersArray($args = [])
	{
		return array_merge([
			'auth' => $GLOBALS['auth'],
			'flash' => $GLOBALS['flash'],
			'baseUrl' => $GLOBALS['baseUrl']
		], $args);
	}

	public static function convertToSpecialWebpageDataWithoutHTML($webpageName, $webpageTitle)
	{
		return [
			'webpage' => [
				'name' => $webpageName,
				'title' => $webpageTitle,
				'HTML' => ''
			]
		];
	}

	public static function convertToSpecialWebpageDataWithHTML($webpageName, $webpageTitle, $view, $filePath)
	{
		return [
			'webpage' => [
				'name' => $webpageName,
				'title' => $webpageTitle,
				'HTML' => $view->fetch("SpecialWebpages/{$filePath}.html")
			]
		];
	}
}