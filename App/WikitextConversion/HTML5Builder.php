<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

use App\Permissions\WebpagePermissionBlock;

class HTML5Builder
{
	private $currentPermissionsExpression;
	private $htmlBlocks;
	private $html;

	public function __construct()
	{
		$this->initialise();
	}

	public function initialise(): void
	{
		$this->currentPermissionsExpression = '';
		$this->htmlBlocks = array();
		$this->html = '';
	}

	private function conclude(): void
	{
		$this->addHtmlBlock( $this->currentPermissionsExpression, $this->html );
	}

	public function getHtmlBlocks(): array
	{
		return $this->htmlBlocks;
	}

	private function addHtmlBlock( string $permissionsExpression, string $html ): void
	{
		$this->htmlBlocks[] = new WebpagePermissionBlock( $permissionsExpression, $html );
	}

	public function getHtml(): string
	{
		$entireHtml = '';
		foreach ( $this->getHtmlBlocks() as $htmlBlock )
			$entireHtml .= $htmlBlock->getHtml();
		return $entireHtml;
	}

	public function add( $tokens ): void
	{
		foreach ( $tokens as $token )
            $this->processToken($token);
	}

	public function build( $tokens ): void
	{
		$this->add( $tokens );
		$this->conclude();
	}

	public function buildAndGetHtmlBlocks( $tokens ): array
	{
		$this->build( $tokens );
		return $this->getHtmlBlocks();
	}

	private function processToken( object $token ): void
	{
		assert( is_a($token, 'App\WikitextConversion\Tokens\BaseToken') );

		if ( is_a($token, 'App\WikitextConversion\Tokens\MetaToken') )
			$this->handleMetaToken($token);
		else
    		$this->html .= $token->toHTML();
	}

	private function handleMetaToken( object $token ): void
	{
		switch ( $token->getName() )
		{
			case 'permissions-specifier':
				$this->addHtmlBlock($this->currentPermissionsExpression, $this->html);
				$this->currentPermissionsExpression = $token->getAttribute('RPN Permissions Expression');
				$this->html = '';
				break;
		}
	}
}