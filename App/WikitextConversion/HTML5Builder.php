<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

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

	private function addHtmlBlock( string: $permissions, string $html ): void
	{
		$htmlBlocks[] = WikitextPermissionBlock($permissions, $html);
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
				$permission = $token->getAttribute('RPN Permissions Expression');
				$this->addHtmlBlock($permission, $this->html);
				$this->html = '';
				break;
		}
	}
}