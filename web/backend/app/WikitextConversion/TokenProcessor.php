<?php

declare(strict_types=1);

namespace App\WikitextConversion;

use App\Permissions\WikiPagePermissionBlock;

class TokenProcessor
{
	public static \App\Logging\Logger $logger;

	// Instance variables
	private bool $usePermissionBlocks;

	// Output variable
	/** [ WikiTextPermissionBlock ] */
	private array $blocks = [];

	// Transient variables used for processing.
	private $currentPermissionsExpression;
	private $html;

	/**
	 * Processes the tokens created by the parser; sets the $blocks member
	 * variables
	 */
	public function process( array $tokens, string $mode = 'inline' ): array|string
	{
		switch ( $mode )
		{
			case 'inline':
				$this->usePermissionBlocks = false;
				break;

			case 'top-level':
				$this->usePermissionBlocks = true;
				break;

			default:
				throw new \InvalidArgumentException( '$mode parameter must be either \'inline\' or \'top-level\'.' );
		}

		self::$logger->info( 'Processing Wikitext tokens' );

		// Ensure member variables are cleared
		$this->initialise();

		// Process all tokens
		foreach ( self::arrayFlatten( $tokens ) as $token ) {
			$this->processToken( $token );
		}

		if ( $this->usePermissionBlocks )
		{
			// Finished iterating through tokens; if there is any HTML left to add to
			// a permission block, add it now.
			if ( $this->html !== '' ) {
				$this->addHtmlBlock( $this->currentPermissionsExpression, $this->html );
			}

			$return = $this->blocks;
		} else {
			if ( ! empty( $this->blocks ) ) {
				throw new \InvalidArgumentException( '$mode requires no permission blocks but permission meta-token given.' );
			}

			$return = $this->html;
		}

		self::$logger->info( 'Finished processing tokens' );

		return $return;
	}

	// == Getters ==

	public function getHtml(): string
	{
		$entireHtml = '';
		foreach ( $this->blocks as $htmlBlock ) {
			$entireHtml .= $htmlBlock->getHtml();
		}
		return $entireHtml;
	}

	// == Processing ==

	private function initialise(): void
	{
		$this->currentPermissionsExpression = '';
		$this->blocks = [];
		$this->html = '';
	}

	private function processToken( object $token ): void
	{
		assert( is_a( $token, 'App\WikitextConversion\Tokens\BaseToken' ) );

		if ( is_a( $token, 'App\WikitextConversion\Tokens\MetaToken' ) )
		{
			switch ( $token->getName() )
			{
				case 'permissions-specifier':
					// About to start new WikiPagePermissionBlock, so add the contents
					// up to now to a WikiPagePermissionBlock and begin a new one.
					$this->addHtmlBlock( $this->currentPermissionsExpression, $this->html );

					// Now begin collecting HTML for next block by setting member variables.
					$this->currentPermissionsExpression = $token->getAttribute( 'RPN Permissions Expression' );
					$this->html = '';

					break;
			}
		} else {
			$this->html .= $token->toHTML();
		}
	}

	private function addHtmlBlock( string $permissionsExpression, string $html ): void
	{
		$this->blocks[] = new WikiPagePermissionBlock( $permissionsExpression, $html );
	}

	private static function arrayFlatten( array $array = null ): array
	{
		$result = [];

		if ( !is_array( $array ) )
		{
			$array = func_get_args();
		}

		foreach ( $array as $key => $value )
		{
			if ( $value === null ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$result = array_merge( $result, self::arrayFlatten( $value ) );
			} else {
				$result = array_merge( $result, array($key => $value) );
			}
		}

		return $result;
	}
}
