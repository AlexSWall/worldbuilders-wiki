<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

use App\Permissions\WikiPagePermissionBlock;

class TokenProcessor
{
	static $logger;

	// Instance variables
	private string $mode;
	private bool $usePermissionBlocks;

	// Output variable
	/** [ WikiTextPermissionBlock ] */
	private $result;

	// Transient variables used for processing.
	private $currentPermissionsExpression;
	private $html;

	/**
	 * Processes the tokens created by the parser to construct the $result
	 * member variables
	 */
	public function process( array $tokens, string $mode = 'inline' )
	{
		$this->mode = $mode;

		switch ( $mode )
		{
			case 'inline':
				$this->usePermissionBlocks = false;
				break;

			case 'top-level':
				$this->usePermissionBlocks = true;
				break;

			default:
				throw new \InvalidArgumentException('$mode parameter must be either \'inline\' or \'top-level\'.');
		}

		self::$logger->addInfo('Processing Wikitext tokens');

		// Ensure member variables are cleared
		$this->initialise();

		// Process all tokens
		foreach ( self::array_flatten($tokens) as $token )
			$this->processToken($token);

		if ( $this->usePermissionBlocks )
		{
			// Finished iterating through tokens; if there is any HTML left to add to
			// a permission block, add it now.
			if ($this->html !== '')
				$this->addHtmlBlock( $this->currentPermissionsExpression, $this->html );
		}
		else
		{
			if ( ! empty($this->result) )
				throw new \InvalidArgumentException('$mode requires no permission blocks but permission meta-token given.');

			$this->result = $this->html;
		}

		self::$logger->addInfo('Finished processing tokens');

		return $this->result;
	}

	// == Getters ==

	public function getHtml(): string
	{
		$entireHtml = '';
		foreach ( $this->result as $htmlBlock )
			$entireHtml .= $htmlBlock->getHtml();
		return $entireHtml;
	}

	// == Processing ==

	private function initialise(): void
	{
		$this->currentPermissionsExpression = '';
		$this->result = array();
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
		$this->result[] = new WikiPagePermissionBlock( $permissionsExpression, $html );
	}

	private static function array_flatten($array = null): array
	{
		$result = array();

		if (!is_array($array))
		{
			$array = func_get_args();
		}

		foreach ($array as $key => $value)
		{
			if ($value === null)
				continue;
			if (is_array($value))
				$result = array_merge($result, self::array_flatten($value));
			else
				$result = array_merge($result, array($key => $value));
		}

		return $result;
	}
}
