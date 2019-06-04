<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextPermissionBlock
{
	private $index;
	private $permissionsExpression;
	private $html;

	public function __construct( int $index, string $permissionsExpression, string $html )
	{
		$this->index = $index;
		$this->permissionsExpression = $permissionsExpression;
		$this->html = $html;
	}

	public function getIndex(): int
	{
		return $this->index;
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