<?php

declare(strict_types=1);

namespace App\WikitextConversion\Tokens;

use App\Models\Infobox;

class InfoboxToken extends BaseToken
{
	public static \App\Logging\Logger $logger;

	private string $infoboxType;
	/** [
	 *      string entryKey => [
	 *          int lineNumber => [
	 *              int tokenNumber => BaseToken tokens
	 *          ]
	 *      ]
	 *  ] $values;
	 */
	private array $values;

	public function __construct( string $infoboxType, array $values = [] )
	{
		$this->infoboxType = $infoboxType;
		$this->values = $values;
	}

	public function toHtml(): string
	{
		$infoboxStructure = Infobox::retrieveInfoboxByName( $this->infoboxType );

		if ( $infoboxStructure === null )
		{
			return '';
		}

		/** [ AbstractInfoboxItem ] */
		$infoboxStructureItems = $infoboxStructure->getInfoboxItems();

		$html = '';

		$html .= '<aside class="infobox">';

		// Set the title
		{
			$titleEntryValue = $this->values['title'];

			if ( $titleEntryValue !== null
				&& count( $titleEntryValue ) === 1
				&& is_a( $titleEntryValue[0], TextToken::class ) )
			{
				// The value for the title key is a single text token; extract the text
				// and set it as the title.
				$title = $titleEntryValue[0]->toHtml();
			}
			else
			{
				// We did not find a value title, so use this generic title.
				$title = 'Infobox Title';
			}

			$html .= '<h2 class="infobox-title">' . $title . '</h2>';
		}


		{
			// Need to keep track of whether we're in a section or not to know
			// whether to add a closing section HTML tag.
			$inSection = false;

			// When we see a subheading of a section, we ensure we have entries for it
			// before placing it now.
			$priorHeader = null;

			// We do the same for line breaks within sections.
			$priorHorizontalRule = null;
			$sectionHasContent = false;

			foreach ( $infoboxStructureItems as /** AbstractInfoboxItem */ $infoboxStructureItem )
			{
				if ( $infoboxStructureItem->isContent() )
				{
					$itemHtml = $infoboxStructureItem->getHtml( $this->values );

					if ( $itemHtml === null )
					{
						continue;
					}

					if ( $priorHeader !== null )
					{
						if ( $inSection )
						{
							$html .= '</section>';
						}

						$html .= '<section>';
						$inSection = true;

						$html .= $priorHeader->getHtml( $this->values );
						$priorHeader = null;
						$priorHorizontalRule = null;
					}
					elseif ( $priorHorizontalRule !== null )
					{
						$html .= $priorHorizontalRule->getHtml( $this->values );
						$priorHorizontalRule = null;
					}

					$html .= $itemHtml;
					$sectionHasContent = true;
				}
				elseif ( $infoboxStructureItem->getTypeString() === 'Subheading' )
				{
					$priorHeader = $infoboxStructureItem;
					$sectionHasContent = false;

					continue;
				}
				elseif ( $infoboxStructureItem->getTypeString() === 'HorizontalRule' )
				{
					if ( $sectionHasContent )
					{
						$priorHorizontalRule = $infoboxStructureItem;
						$sectionHasContent = false;
					}

					continue;
				}
				else
				{
					self::$logger->info( "InfoboxToken::toHtml else statement: shouldn't get here" );
				}
			}

			if ( $inSection )
			{
				$html .= '</section>';
			}
		}

		self::$logger->info( 'HTML produced: ' . print_r( $html, true ) );

		$html .= '</aside>';

		return $html;
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'infobox-type' => $this->infoboxType,
			'values' => $this->values
		];
	}
}
