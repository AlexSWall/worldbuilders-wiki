<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextPermissionBlock
{
	private $permissionsExpression;
	private $html;

	public function __construct( string: $permissionsExpression, string $html )
	{
		$this->permissionsExpression = $permissionsExpression;
		$this->html = $html;
	}

	public function getPermissionsExpression(): string
	{
		return $this->permissionsExpression;
	}

	public function getHtml(): string
	{
		return $this->html;
	}
}