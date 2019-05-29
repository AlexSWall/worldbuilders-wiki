<?php

namespace App\WikitextConversion;

use hafriedlander\Peg\Parser;

class Grammar extends Parser\Basic
{
	/*!* Grammar

	Start: tlb* newlineToken*
		function Name {
			$this->emitChunk( [ new EndOfFileToken() ] );
		}

	*/
}

