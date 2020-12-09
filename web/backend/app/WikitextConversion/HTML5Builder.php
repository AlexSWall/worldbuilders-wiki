<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

use App\Permissions\WikiPagePermissionBlock;

class HTML5Builder
{
	static $logger;

	private $htmlBlocks;

	// Transient variables used for processing.
	private $currentPermissionsExpression;
	private $html;

	/**
	 * Builds the $htmlBlocks member variable.
	 *
	 * Builds up the $htmlBlocks member variable by iterating through the
	 * supplied tokens and creating `WikiTextPermissionBlock`s for each
	 * permission block in the WikiText.
	 */
	public function build( array $tokens ): void
	{
		self::$logger->addInfo('Building HTML blocks from Wikitext tokens');

		// Ensure member variables are cleared
		$this->initialise();

		// Process all tokens, building up permission blocks.
		foreach ( $tokens as $token )
			$this->processToken($token);

		// Finished iterating through tokens; if there is any HTML left to add to
		// a permission block, add it now.
		if ($this->html !== '')
			$this->addHtmlBlock( $this->currentPermissionsExpression, $this->html );

		self::$logger->addInfo('Finished building HTML block');
	}

	// == Getters ==

	public function getHtmlBlocks(): array
	{
		return $this->htmlBlocks;
	}

	public function getHtml(): string
	{
		$entireHtml = '';
		foreach ( $this->getHtmlBlocks() as $htmlBlock )
			$entireHtml .= $htmlBlock->getHtml();
		return $entireHtml;
	}

	// == Processing ==

	private function initialise(): void
	{
		$this->currentPermissionsExpression = '';
		$this->htmlBlocks = array();
		$this->html = '';
	}

	private function processToken( object $token ): void
	{
		assert( is_a($token, 'App\WikitextConversion\Tokens\BaseToken') );

		if ( is_a($token, 'App\WikitextConversion\Tokens\MetaToken') )
			switch ( $token->getName() )
			{
				case 'permissions-specifier':
					// About to start new WikiPagePermissionBlock, so add the contents
					// up to now to a WikiPagePermissionBlock and begin a new one.
					$this->addHtmlBlock($this->currentPermissionsExpression, $this->html);

					// Now begin collecting HTML for next block by setting member variables.
					$this->currentPermissionsExpression = $token->getAttribute('RPN Permissions Expression');
					$this->html = '';

					break;
			}
		else
    		$this->html .= $token->toHTML();
	}

	private function addHtmlBlock( string $permissionsExpression, string $html ): void
	{
		$this->htmlBlocks[] = new WikiPagePermissionBlock( $permissionsExpression, $html );
	}
}
