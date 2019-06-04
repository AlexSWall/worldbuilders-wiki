<?php





namespace App\WikitextConversion;

use App\WikitextConversion\Tokens\ClosingTagToken;
use App\WikitextConversion\Tokens\EndOfFileToken;
use App\WikitextConversion\Tokens\MetaToken;
use App\WikitextConversion\Tokens\NewLineToken;
use App\WikitextConversion\Tokens\OpeningTagToken;
use App\WikitextConversion\Tokens\SelfClosingTagToken;
use App\WikitextConversion\Tokens\TextToken;

class Grammar
{
	static function getNewGrammarParser()
	{
		return new PEGParser();
	}
}


class PEGParser extends \WikiPEG\PEGParserBase {
  // initializer
  
  
  private function array_flatten($array = null) {
  	$result = array();
  
  	if (!is_array($array)) {
  		$array = func_get_args();
  	}
  
  	foreach ($array as $key => $value) {
  		if (is_array($value))
  			$result = array_merge($result, array_flatten($value));
  		else
  			$result = array_merge($result, array($key => $value));
  	}
  
  	return $result;
  }
  
  private function startOffset() {
  	return $this->savedPos;
  }
  
  private function endOffset() {
  	return $this->currPos;
  }
  
  private function createListTokenArray( $listItems )
  {
  	$previousPrefix = '';
  	$listTokens = array();
  
  	$prefixToTagText = [
  		'*' => 'ul',
  		'#' => 'ol'
  	];
  
  	foreach ( $listItems as $item )
  	{
  		[ 'prefix' => $newPrefix, 'content' => $itemContent ] = $item;
  
  		if ( $previousPrefix !== $newPrefix )
  		{
  			/* Opening and/or closing tags will need to be added for lists. */
  
  			/* Determine where the first difference is. */
  			$indexOfFirstDifference = strspn($previousPrefix ^ $newPrefix, "\0");
  
  			$itemClosesLists = $indexOfFirstDifference < strlen($previousPrefix);
  			$itemOpensNewLists = $indexOfFirstDifference < strlen($newPrefix);
  
  			if ( $itemClosesLists || !$itemOpensNewLists )
  			{
  				/* We must close the previous list item's tag. */
  				$listTokens[] = new ClosingTagToken('li');
  			}
  
  			/* Add closing tags for each previously open list opened after the
  			 * first difference between the two prefixes. */
  			if ( $itemClosesLists )
  			{
  				$listsToClose = substr( $previousPrefix, $indexOfFirstDifference );
  
  				for ( $i = strlen( $listsToClose ) - 1; $i >= 0; $i-- )
  				{
  					$tagText = $prefixToTagText[ $listsToClose[$i] ];
  					$listTokens[] = new ClosingTagToken($tagText);
  
  					/* Close the list item which the sublist was contained in. */
  					$listTokens[] = new ClosingTagToken('li');
  				}
  			}
  
  			if ( isset($item['end']) )
  				$listTokens[] = $item['end'];
  
  			/* Add opening tags for each newly opened list. */
  			if ( $itemOpensNewLists )
  			{
  				$listsToOpen = substr( $newPrefix, $indexOfFirstDifference );
  
  				for ( $i = 0; $i < strlen( $listsToOpen ); $i++ )
  				{
  					$tagText = $prefixToTagText[ $listsToOpen[$i] ];
  					$listTokens[] = new OpeningTagToken($tagText);
  				}
  			}
  		} /* Finished closing and opening list tags. */
  		else
  		{
  			/* We must close the previous list item's tag. */
  			$listTokens[] = new ClosingTagToken('li');
  
  			if ( isset($item['end']) )
  				$listTokens[] = $item['end'];
  		}
  
  		$listTokens[] = new OpeningTagToken('li');
  		$listTokens = array_merge($listTokens, $itemContent);
  		
  		$previousPrefix = $newPrefix;
  	}
  
  	$listTokens[] = new ClosingTagToken('li'); /* Close the last item. */
  
  	for ( $i = strlen( $previousPrefix ) - 1; $i >= 0; $i-- )
  	{
  		$tagText = $prefixToTagText[ $previousPrefix[$i] ];
  		$listTokens[] = new ClosingTagToken($tagText);
  
  		if ( $i > 0 )
  		{
  			/* Close the list item which the sublist was contained in. */
  			$listTokens[] = new ClosingTagToken('li');
  		}
  	}
  
  	return $listTokens;
  }
  
  

  // cache init
  

  // expectations
  protected $expectations = [
    0 => ["type" => "end", "description" => "end of input"],
    1 => ["type" => "other", "description" => "start"],
    2 => ["type" => "literal", "value" => "\x0a", "description" => "\"\\n\""],
    3 => ["type" => "literal", "value" => "\x0d\x0a", "description" => "\"\\r\\n\""],
    4 => ["type" => "class", "value" => "[ \\t]", "description" => "[ \\t]"],
    5 => ["type" => "literal", "value" => "==", "description" => "\"==\""],
    6 => ["type" => "literal", "value" => "=", "description" => "\"=\""],
    7 => ["type" => "literal", "value" => "|", "description" => "\"|\""],
    8 => ["type" => "any", "description" => "any character"],
    9 => ["type" => "literal", "value" => "or", "description" => "\"or\""],
    10 => ["type" => "literal", "value" => "||", "description" => "\"||\""],
    11 => ["type" => "class", "value" => "[*#;:]", "description" => "[*#;:]"],
    12 => ["type" => "literal", "value" => "(", "description" => "\"(\""],
    13 => ["type" => "literal", "value" => ")", "description" => "\")\""],
    14 => ["type" => "literal", "value" => "and", "description" => "\"and\""],
    15 => ["type" => "literal", "value" => "&&", "description" => "\"&&\""],
    16 => ["type" => "literal", "value" => "[[", "description" => "\"[[\""],
    17 => ["type" => "literal", "value" => "image:", "description" => "\"image:\""],
    18 => ["type" => "literal", "value" => ",", "description" => "\",\""],
    19 => ["type" => "literal", "value" => "]]", "description" => "\"]]\""],
    20 => ["type" => "literal", "value" => "'''", "description" => "\"'''\""],
    21 => ["type" => "literal", "value" => "''", "description" => "\"''\""],
    22 => ["type" => "class", "value" => "[0-9]", "description" => "[0-9]"],
  ];

  // actions
  private function a0($blocks) {
  
  		return array_flatten([ $blocks, new EndOfFileToken() ]);
  	
  }
  private function a1($nl) {
  
  		return $nl;
  	
  }
  private function a2() {
   return [ new NewLineToken() ]; 
  }
  private function a3() {
   return $this->endOffset() === $this->inputLength; 
  }
  private function a4() {
  
  		return [];
  	
  }
  private function a5($start, $block) {
  
  		return array_merge( $start, $block );
  	
  }
  private function a6($start, $content) {
  
  		return [$start, $content];
  	
  }
  private function a7($lines) {
  
  		$tokens = array();
  
  		$firstLine = $lines[0];
  		$innerLines = array_slice($lines, 1, -1);
  		$lastLine = end($lines);
  
  		// A line is a pair [newline, content].
  
  		$tokens[] = $firstLine[0];
  		$tokens[] = new OpeningTagToken('p');
  		$tokens = array_merge($tokens, $firstLine[1]);
  
  		foreach( $innerLines as $innerLinePair )
  		{
  			$tokens[] = $innerLinePair[0];
  			$tokens = array_merge($tokens, $innerLinePair[1]);
  		}
  
  		if( sizeof($lines) > 1 )
  		{
  			$tokens[] = $lastLine[0];
  			$tokens = array_merge($tokens, $lastLine[1]);
  		}
  		$tokens[] = new ClosingTagToken('p');
  
  		return $tokens;
  	
  }
  private function a8($newline1, $newline2) {
  
  		return array_merge( $newline1, $newline2 );
  	
  }
  private function a9($line) {
  
  
  		if ( is_a($line[0], TextToken::class) )
  			$line[0]->ltrim();
  
  		if ( is_a( end($line), TextToken::class ) )
  			end($line)->rtrim();
  
  		return $line;
  	
  }
  private function a10() {
   return $this->endOffset() === 0; 
  }
  private function a11($extrasLeft, $inner, $rpnPermissionsStr) {
   
  			return $rpnPermissionsStr;
  		
  }
  private function a12($extrasLeft, $inner, $rpnPermissionsString, $extrasRight) {
  
  		$level = strval( 2 + min( count($extrasLeft), count($extrasRight) ) );
  		$textToken = new TextToken($inner);
  		$textToken->trim();
  
  		$tokens = array();
  
  		if ( $rpnPermissionsString )
  			$tokens[] = new MetaToken('permissions-specifier', [
  				'RPN Permissions Expression' => $rpnPermissionsString
  			]);
  
  		return array_merge( $tokens, [
  			new OpeningTagToken('h' . $level),
  			$textToken,
  			new ClosingTagToken('h' . $level)
  		]);
  	
  }
  private function a13($firstItem, $end, $item) {
   return array_merge( [ 'end' => $end ], $item ); 
  }
  private function a14($firstItem, $otherItems) {
  
  		$listItems = array_merge([ $firstItem ], $otherItems);
  		return $this->createListTokenArray($listItems);
  	
  }
  private function a15($element) {
   return $element; 
  }
  private function a16($content) {
  
  		$lineContents = [];
  		$textBuffer = '';
  		foreach( $content as $element )
  		{
  			if ( is_string( $element ) )
  				$textBuffer .= $element;
  			else
  			{
  				if ( $textBuffer !== '' )
  				{
  					$lineContents[] = new TextToken( $textBuffer );
  					$textBuffer = '';
  				}
  				$lineContents[] = $element;
  			}
  		}
  		if ( $textBuffer !== '' )
  			$lineContents[] = new TextToken( $textBuffer );
  		
  		return $lineContents;
  	
  }
  private function a17($c) {
   return $c; 
  }
  private function a18($text) {
  
  		return [ new TextToken( implode('', $text) ) ];
  	
  }
  private function a19($firstAndExpression, $orOperation, $andGroup) {
   
  			return [ $orOperation, $andGroup ];
  		
  }
  private function a20($firstAndExpression, $others) {
  
  		$expressionRPN = $firstAndExpression;
  		foreach ( $others as $operatorPermissionGroupPair )
  			$expressionRPN .= ' ' . $operatorPermissionGroupPair[1] . ' ' . $operatorPermissionGroupPair[0];
  		return $expressionRPN;
  	
  }
  private function a21($prefix, $content) {
  
  		return [ 'prefix' => $prefix, 'content' => $content ?: [ new TextToken('') ] ];
  	
  }
  private function a22($firstPermissionGroup, $andOperation, $permGroup) {
   
  			return [ $andOperation, $permGroup ];
  		
  }
  private function a23($firstPermissionGroup, $others) {
  
  		$expressionRPN = $firstPermissionGroup;
  		foreach ( $others as $operatorPermissionGroupPair )
  			$expressionRPN .= ' ' . $operatorPermissionGroupPair[1] . ' ' . $operatorPermissionGroupPair[0];
  		return $expressionRPN;
  	
  }
  private function a24() {
   return '||'; 
  }
  private function a25($expr) {
   return $expr; 
  }
  private function a26() {
   return '&&'; 
  }
  private function a27($imageFileName, $width, $height) {
  
  			return [ $width, $height ];
  		
  }
  private function a28($imageFileName, $dimensions) {
  
  		$tokenAttributes = [ 'src' => '/images/wiki-images/' . trim($imageFileName) ];
  
  		if ( $dimensions )
  		{
  			[ $width, $height ] = $dimensions;
  			$tokenAttributes['width'] = $width;
  			$tokenAttributes['height'] = $height;
  		}
  
  		return [
  			new OpeningTagToken('img', $tokenAttributes),
  		];
  	
  }
  private function a29($target, $text) {
   return $text; 
  }
  private function a30($target, $displayText) {
  
  		$linkTarget = '\'/#' . ucwords(str_replace(' ', '_', trim($target)), '_-') . '\'';
  		$linkText = trim($displayText ?: $target);
  		return [
  			new OpeningTagToken('a', [
  				'href' => $linkTarget
  			]),
  			new TextToken( trim($linkText) ),
  			new ClosingTagToken('a')
  		];
  	
  }
  private function a31($content) {
  
  		return array_merge(
  			[ new OpeningTagToken('b') ],
  			$content,
  			[ new ClosingTagToken('b') ]
  		);
  	
  }
  private function a32($content) {
  
  		return array_merge(
  			[ new OpeningTagToken('i') ],
  			$content,
  			[ new ClosingTagToken('i') ]
  		);
  	
  }
  private function a33($numberString) {
   return intval($numberString); 
  }

  // generated
  private function parsestart($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = [];
    for (;;) {
      $r5 = $this->parseblock(true);
      if ($r5!==self::$FAILED) {
        $r4[] = $r5;
      } else {
        break;
      }
    }
    if (count($r4) === 0) {
      $r4 = self::$FAILED;
    }
    // blocks <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r5
    for (;;) {
      $r6 = $this->discardnewLine(true);
      if ($r6===self::$FAILED) {
        break;
      }
    }
    // free $r6
    $r5 = true;
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r5
    $r5 = $this->discardendOfFile(true);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a0($r4);
    } else {
      if (!$silence) {$this->fail(1);}
    }
    // free $p3
    return $r1;
  }
  private function parseblock($silence) {
    // start choice_1
    $r1 = $this->parseblockLines($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseparagraphLines($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsenewLine($silence);
    // nl <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a1($r4);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function discardnewLine($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\x0a") {
      $this->currPos++;
      $r1 = "\x0a";
      $this->savedPos = $p2;
      $r1 = $this->a2();
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(2);}
      $r1 = self::$FAILED;
    }
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "\x0d\x0a", $this->currPos, 2, false) === 0) {
      $r1 = "\x0d\x0a";
      $this->currPos += 2;
      $this->savedPos = $p3;
      $r1 = $this->a2();
    } else {
      if (!$silence) {$this->fail(3);}
      $r1 = self::$FAILED;
    }
    choice_1:
    return $r1;
  }
  private function discardendOfFile($silence) {
    $p2 = $this->currPos;
    $this->savedPos = $this->currPos;
    $r1 = $this->a3();
    if ($r1) {
      $r1 = false;
      $this->savedPos = $p2;
      $r1 = $this->a4();
    } else {
      $r1 = self::$FAILED;
    }
    return $r1;
  }
  private function parseblockLines($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestartOfLine($silence);
    // start <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseblockLine($silence);
    // block <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a5($r4, $r6);
    }
    // free $p3
    return $r1;
  }
  private function parseparagraphLines($silence) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_1
      $p6 = $this->currPos;
      $p7 = $this->currPos;
      $r8 = $this->discardparagraphBreak(true);
      if ($r8 === self::$FAILED) {
        $r8 = false;
      } else {
        $r8 = self::$FAILED;
        $this->currPos = $p7;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      // free $p7
      $r9 = $this->parsestartOfLine($silence);
      // start <- $r9
      if ($r9===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r10 = $this->discardanySpacing($silence);
      if ($r10===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r11 = $this->parsetrimmedInlineLine($silence);
      // content <- $r11
      if ($r11===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r4 = true;
      seq_1:
      if ($r4!==self::$FAILED) {
        $this->savedPos = $p5;
        $r4 = $this->a6($r9, $r11);
        $r3[] = $r4;
      } else {
        break;
      }
      // free $p6
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // lines <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a7($r3);
    }
    return $r1;
  }
  private function parsenewLine($silence) {
    // start choice_1
    $p2 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "\x0a") {
      $this->currPos++;
      $r1 = "\x0a";
      $this->savedPos = $p2;
      $r1 = $this->a2();
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(2);}
      $r1 = self::$FAILED;
    }
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "\x0d\x0a", $this->currPos, 2, false) === 0) {
      $r1 = "\x0d\x0a";
      $this->currPos += 2;
      $this->savedPos = $p3;
      $r1 = $this->a2();
    } else {
      if (!$silence) {$this->fail(3);}
      $r1 = self::$FAILED;
    }
    choice_1:
    return $r1;
  }
  private function discardanySpacing($silence) {
    $p1 = $this->currPos;
    for (;;) {
      $r3 = $this->discardspacing($silence);
      if ($r3===self::$FAILED) {
        break;
      }
    }
    // free $r3
    $r2 = true;
    if ($r2!==self::$FAILED) {
      $r2 = substr($this->input, $p1, $this->currPos - $p1);
    } else {
      $r2 = self::$FAILED;
    }
    // free $p1
    return $r2;
  }
  private function parsestartOfLine($silence) {
    // start choice_1
    $r1 = $this->parsenewLine($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parsestartOfFile($silence);
    choice_1:
    return $r1;
  }
  private function parseblockLine($silence) {
    // start choice_1
    $r1 = $this->parseheader($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parselist($silence);
    choice_1:
    return $r1;
  }
  private function discardparagraphBreak($silence) {
    // start choice_1
    $r1 = $this->discardblockLines($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsenewLine($silence);
    // newline1 <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parsenewLine($silence);
    // newline2 <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a8($r4, $r6);
    }
    // free $p3
    choice_1:
    return $r1;
  }
  private function parsetrimmedInlineLine($silence) {
    $p2 = $this->currPos;
    $r3 = $this->parseinlineLine($silence, 0x0);
    // line <- $r3
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a9($r3);
    }
    return $r1;
  }
  private function discardspacing($silence) {
    $r1 = $this->input[$this->currPos] ?? '';
    if ($r1 === " " || $r1 === "\x09") {
      $this->currPos++;
    } else {
      $r1 = self::$FAILED;
      if (!$silence) {$this->fail(4);}
    }
    return $r1;
  }
  private function parsestartOfFile($silence) {
    $p2 = $this->currPos;
    $this->savedPos = $this->currPos;
    $r1 = $this->a10();
    if ($r1) {
      $r1 = false;
      $this->savedPos = $p2;
      $r1 = $this->a4();
    } else {
      $r1 = self::$FAILED;
    }
    return $r1;
  }
  private function parseheader($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->discardanySpacing($silence);
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r5 = "==";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(5);}
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = [];
    for (;;) {
      if (($this->input[$this->currPos] ?? null) === "=") {
        $this->currPos++;
        $r7 = "=";
        $r6[] = $r7;
      } else {
        if (!$silence) {$this->fail(6);}
        $r7 = self::$FAILED;
        break;
      }
    }
    // extrasLeft <- $r6
    // free $r7
    $p8 = $this->currPos;
    $r7 = $this->discardinlineText($silence, 0x1);
    // inner <- $r7
    if ($r7!==self::$FAILED) {
      $r7 = substr($this->input, $p8, $this->currPos - $p8);
    } else {
      $r7 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p8
    $p8 = $this->currPos;
    // start seq_2
    $p10 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r11 = "|";
    } else {
      if (!$silence) {$this->fail(7);}
      $r11 = self::$FAILED;
      $r9 = self::$FAILED;
      goto seq_2;
    }
    $r12 = $this->discardanySpacing($silence);
    if ($r12===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_2;
    }
    // start choice_1
    $r13 = $this->parseorExpressionGroup($silence);
    if ($r13!==self::$FAILED) {
      goto choice_1;
    }
    $r13 = '';
    choice_1:
    // rpnPermissionsStr <- $r13
    if ($r13===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_2;
    }
    $r14 = $this->discardanySpacing($silence);
    if ($r14===self::$FAILED) {
      $this->currPos = $p10;
      $r9 = self::$FAILED;
      goto seq_2;
    }
    $r9 = true;
    seq_2:
    if ($r9!==self::$FAILED) {
      $this->savedPos = $p8;
      $r9 = $this->a11($r6, $r7, $r13);
    } else {
      $r9 = null;
    }
    // free $p10
    // rpnPermissionsString <- $r9
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r15 = "==";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(5);}
      $r15 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r16 = [];
    for (;;) {
      if (($this->input[$this->currPos] ?? null) === "=") {
        $this->currPos++;
        $r17 = "=";
        $r16[] = $r17;
      } else {
        if (!$silence) {$this->fail(6);}
        $r17 = self::$FAILED;
        break;
      }
    }
    // extrasRight <- $r16
    // free $r17
    $r17 = $this->discardanySpacing($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a12($r6, $r7, $r9, $r16);
    }
    // free $p3
    return $r1;
  }
  private function parselist($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parselistItem($silence);
    // firstItem <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p5 = $this->currPos;
    $r6 = $this->discardendOfLine(true);
    if ($r6!==self::$FAILED) {
      $r6 = false;
      $this->currPos = $p5;
    } else {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r7 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_2
      $p9 = $this->currPos;
      $r10 = $this->parseendOfLine($silence);
      // end <- $r10
      if ($r10===self::$FAILED) {
        $r8 = self::$FAILED;
        goto seq_2;
      }
      $r11 = $this->parselistItem($silence);
      // item <- $r11
      if ($r11===self::$FAILED) {
        $this->currPos = $p9;
        $r8 = self::$FAILED;
        goto seq_2;
      }
      $p12 = $this->currPos;
      $r13 = $this->discardendOfLine(true);
      if ($r13!==self::$FAILED) {
        $r13 = false;
        $this->currPos = $p12;
      } else {
        $this->currPos = $p9;
        $r8 = self::$FAILED;
        goto seq_2;
      }
      // free $p12
      $r8 = true;
      seq_2:
      if ($r8!==self::$FAILED) {
        $this->savedPos = $p5;
        $r8 = $this->a13($r4, $r10, $r11);
        $r7[] = $r8;
      } else {
        break;
      }
      // free $p9
    }
    // otherItems <- $r7
    // free $r8
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a14($r4, $r7);
    }
    // free $p3
    return $r1;
  }
  private function discardblockLines($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsestartOfLine($silence);
    // start <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseblockLine($silence);
    // block <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a5($r4, $r6);
    }
    // free $p3
    return $r1;
  }
  private function parseinlineLine($silence, $boolParams) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_1
      $p6 = $this->currPos;
      $p7 = $this->currPos;
      $r8 = $this->discardinlineBreak(true, $boolParams);
      if ($r8 === self::$FAILED) {
        $r8 = false;
      } else {
        $r8 = self::$FAILED;
        $this->currPos = $p7;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      // free $p7
      // start choice_1
      $r9 = $this->parseinlineElement($silence, $boolParams);
      if ($r9!==self::$FAILED) {
        goto choice_1;
      }
      if ($this->currPos < $this->inputLength) {
        $r9 = self::consumeChar($this->input, $this->currPos);;
      } else {
        $r9 = self::$FAILED;
        if (!$silence) {$this->fail(8);}
      }
      choice_1:
      // element <- $r9
      if ($r9===self::$FAILED) {
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r4 = true;
      seq_1:
      if ($r4!==self::$FAILED) {
        $this->savedPos = $p5;
        $r4 = $this->a15($r9);
        $r3[] = $r4;
      } else {
        break;
      }
      // free $p6
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // content <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a16($r3);
    }
    return $r1;
  }
  private function discardinlineText($silence, $boolParams) {
    $p2 = $this->currPos;
    $r3 = [];
    for (;;) {
      $p5 = $this->currPos;
      // start seq_1
      $p6 = $this->currPos;
      $p7 = $this->currPos;
      $r8 = $this->discardinlineBreak(true, $boolParams);
      if ($r8 === self::$FAILED) {
        $r8 = false;
      } else {
        $r8 = self::$FAILED;
        $this->currPos = $p7;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      // free $p7
      // c <- $r9
      if ($this->currPos < $this->inputLength) {
        $r9 = self::consumeChar($this->input, $this->currPos);;
      } else {
        $r9 = self::$FAILED;
        if (!$silence) {$this->fail(8);}
        $this->currPos = $p6;
        $r4 = self::$FAILED;
        goto seq_1;
      }
      $r4 = true;
      seq_1:
      if ($r4!==self::$FAILED) {
        $this->savedPos = $p5;
        $r4 = $this->a17($r9);
        $r3[] = $r4;
      } else {
        break;
      }
      // free $p6
    }
    if (count($r3) === 0) {
      $r3 = self::$FAILED;
    }
    // text <- $r3
    // free $r4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a18($r3);
    }
    return $r1;
  }
  private function parseorExpressionGroup($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parseandExpressionGroup($silence);
    // firstAndExpression <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = [];
    for (;;) {
      $p7 = $this->currPos;
      // start seq_2
      $p8 = $this->currPos;
      $r9 = $this->discardanySpacing($silence);
      if ($r9===self::$FAILED) {
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r10 = $this->parseorBooleanOperator($silence);
      // orOperation <- $r10
      if ($r10===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r11 = $this->discardanySpacing($silence);
      if ($r11===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r12 = $this->parseandExpressionGroup($silence);
      // andGroup <- $r12
      if ($r12===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r6 = true;
      seq_2:
      if ($r6!==self::$FAILED) {
        $this->savedPos = $p7;
        $r6 = $this->a19($r4, $r10, $r12);
        $r5[] = $r6;
      } else {
        break;
      }
      // free $p8
    }
    // others <- $r5
    // free $r6
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a20($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parselistItem($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->discardanySpacing($silence);
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    $r5 = self::$FAILED;
    for (;;) {
      $r7 = $this->discardlistCharacter($silence);
      if ($r7!==self::$FAILED) {
        $r5 = true;
      } else {
        break;
      }
    }
    // prefix <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $r7
    // free $p6
    $r7 = $this->discardanySpacing($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r8 = $this->parsetrimmedInlineLine($silence);
    if ($r8===self::$FAILED) {
      $r8 = null;
    }
    // content <- $r8
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a21($r5, $r8);
    }
    // free $p3
    return $r1;
  }
  private function discardendOfLine($silence) {
    // start choice_1
    $r1 = $this->discardnewLine($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->discardendOfFile($silence);
    choice_1:
    return $r1;
  }
  private function parseendOfLine($silence) {
    // start choice_1
    $r1 = $this->parsenewLine($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseendOfFile($silence);
    choice_1:
    return $r1;
  }
  private function discardinlineBreak($silence, $boolParams) {
    // start choice_1
    $p2 = $this->currPos;
    $r1 = $this->discardnewLine(true);
    if ($r1!==self::$FAILED) {
      $r1 = false;
      $this->currPos = $p2;
      goto choice_1;
    }
    // free $p2
    // start seq_1
    $p2 = $this->currPos;
    if (/*header*/($boolParams & 0x1) !== 0) {
      $r3 = false;
    } else {
      $r3 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p4 = $this->currPos;
    // start choice_2
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r5 = "==";
      $this->currPos += 2;
      goto choice_2;
    } else {
      $r5 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r5 = "|";
    } else {
      $r5 = self::$FAILED;
    }
    choice_2:
    if ($r5!==self::$FAILED) {
      $r5 = false;
      $this->currPos = $p4;
    } else {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_2
    $p2 = $this->currPos;
    if (/*bold*/($boolParams & 0x4) !== 0) {
      $r6 = false;
    } else {
      $r6 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $p4 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r7 = "'''";
      $this->currPos += 3;
      $r7 = false;
      $this->currPos = $p4;
    } else {
      $r7 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // free $p4
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_3
    $p2 = $this->currPos;
    if (/*italics*/($boolParams & 0x8) !== 0) {
      $r8 = false;
    } else {
      $r8 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    $p4 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r9 = "''";
      $this->currPos += 2;
      $r9 = false;
      $this->currPos = $p4;
    } else {
      $r9 = self::$FAILED;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    // free $p4
    $p4 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r10 = "'''";
      $this->currPos += 3;
    } else {
      $r10 = self::$FAILED;
    }
    if ($r10 === self::$FAILED) {
      $r10 = false;
    } else {
      $r10 = self::$FAILED;
      $this->currPos = $p4;
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_3;
    }
    // free $p4
    $r1 = true;
    seq_3:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_4
    $p2 = $this->currPos;
    if (/*template*/($boolParams & 0x2) !== 0) {
      $r11 = false;
    } else {
      $r11 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    $p4 = $this->currPos;
    // start choice_3
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "]]", $this->currPos, 2, false) === 0) {
      $r12 = "]]";
      $this->currPos += 2;
      goto choice_3;
    } else {
      $r12 = self::$FAILED;
    }
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r12 = "|";
    } else {
      $r12 = self::$FAILED;
    }
    choice_3:
    if ($r12!==self::$FAILED) {
      $r12 = false;
      $this->currPos = $p4;
    } else {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_4;
    }
    // free $p4
    $r1 = true;
    seq_4:
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    // free $p2
    // start seq_5
    $p2 = $this->currPos;
    if (/*permission*/($boolParams & 0x10) !== 0) {
      $r13 = false;
    } else {
      $r13 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    $p4 = $this->currPos;
    // start choice_4
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "==", $this->currPos, 2, false) === 0) {
      $r14 = "==";
      $this->currPos += 2;
      goto choice_4;
    } else {
      $r14 = self::$FAILED;
    }
    $r14 = $this->discardspacing(true);
    choice_4:
    if ($r14!==self::$FAILED) {
      $r14 = false;
      $this->currPos = $p4;
    } else {
      $this->currPos = $p2;
      $r1 = self::$FAILED;
      goto seq_5;
    }
    // free $p4
    $r1 = true;
    seq_5:
    // free $p2
    choice_1:
    return $r1;
  }
  private function parseinlineElement($silence, $boolParams) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $p4 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "[[", $this->currPos, 2, false) === 0) {
      $r5 = "[[";
      $this->currPos += 2;
      $r5 = false;
      $this->currPos = $p4;
    } else {
      $r5 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p4
    $r6 = $this->parsetemplateElement($silence, $boolParams);
    // element <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a15($r6);
      goto choice_1;
    }
    // free $p3
    $p3 = $this->currPos;
    // start seq_2
    $p4 = $this->currPos;
    $p7 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r8 = "''";
      $this->currPos += 2;
      $r8 = false;
      $this->currPos = $p7;
    } else {
      $r8 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    // free $p7
    $r9 = $this->parsequotedContent($silence);
    // element <- $r9
    if ($r9===self::$FAILED) {
      $this->currPos = $p4;
      $r1 = self::$FAILED;
      goto seq_2;
    }
    $r1 = true;
    seq_2:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p3;
      $r1 = $this->a15($r9);
    }
    // free $p4
    choice_1:
    return $r1;
  }
  private function parseandExpressionGroup($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    $r4 = $this->parsepermissionGroup($silence);
    // firstPermissionGroup <- $r4
    if ($r4===self::$FAILED) {
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = [];
    for (;;) {
      $p7 = $this->currPos;
      // start seq_2
      $p8 = $this->currPos;
      $r9 = $this->discardanySpacing($silence);
      if ($r9===self::$FAILED) {
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r10 = $this->parseandBooleanOperator($silence);
      // andOperation <- $r10
      if ($r10===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r11 = $this->discardanySpacing($silence);
      if ($r11===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r12 = $this->parsepermissionGroup($silence);
      // permGroup <- $r12
      if ($r12===self::$FAILED) {
        $this->currPos = $p8;
        $r6 = self::$FAILED;
        goto seq_2;
      }
      $r6 = true;
      seq_2:
      if ($r6!==self::$FAILED) {
        $this->savedPos = $p7;
        $r6 = $this->a22($r4, $r10, $r12);
        $r5[] = $r6;
      } else {
        break;
      }
      // free $p8
    }
    // others <- $r5
    // free $r6
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a23($r4, $r5);
    }
    // free $p3
    return $r1;
  }
  private function parseorBooleanOperator($silence) {
    $p2 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "or", $this->currPos, 2, true) === 0) {
      $r1 = substr($this->input, $this->currPos, 2);
      $this->currPos += 2;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(9);}
      $r1 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "||", $this->currPos, 2, false) === 0) {
      $r1 = "||";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(10);}
      $r1 = self::$FAILED;
    }
    choice_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a24();
    }
    return $r1;
  }
  private function discardlistCharacter($silence) {
    if (strspn($this->input, "*#;:", $this->currPos, 1) !== 0) {
      $r1 = $this->input[$this->currPos++];
    } else {
      $r1 = self::$FAILED;
      if (!$silence) {$this->fail(11);}
    }
    return $r1;
  }
  private function parseendOfFile($silence) {
    $p2 = $this->currPos;
    $this->savedPos = $this->currPos;
    $r1 = $this->a3();
    if ($r1) {
      $r1 = false;
      $this->savedPos = $p2;
      $r1 = $this->a4();
    } else {
      $r1 = self::$FAILED;
    }
    return $r1;
  }
  private function parsetemplateElement($silence, $boolParams) {
    // start choice_1
    $r1 = $this->parseimage($silence, $boolParams);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parsewikilink($silence, $boolParams);
    choice_1:
    return $r1;
  }
  private function parsequotedContent($silence) {
    // start choice_1
    $r1 = $this->parsebold($silence);
    if ($r1!==self::$FAILED) {
      goto choice_1;
    }
    $r1 = $this->parseitalics($silence);
    choice_1:
    return $r1;
  }
  private function parsepermissionGroup($silence) {
    // start choice_1
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "(") {
      $this->currPos++;
      $r4 = "(";
    } else {
      if (!$silence) {$this->fail(12);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r6 = $this->parseorExpressionGroup($silence);
    // expr <- $r6
    if ($r6===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discardanySpacing($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if (($this->input[$this->currPos] ?? null) === ")") {
      $this->currPos++;
      $r8 = ")";
    } else {
      if (!$silence) {$this->fail(13);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a25($r6);
      goto choice_1;
    }
    // free $p3
    $r1 = $this->parsepermission($silence);
    choice_1:
    return $r1;
  }
  private function parseandBooleanOperator($silence) {
    $p2 = $this->currPos;
    // start choice_1
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "and", $this->currPos, 3, true) === 0) {
      $r1 = substr($this->input, $this->currPos, 3);
      $this->currPos += 3;
      goto choice_1;
    } else {
      if (!$silence) {$this->fail(14);}
      $r1 = self::$FAILED;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "&&", $this->currPos, 2, false) === 0) {
      $r1 = "&&";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(15);}
      $r1 = self::$FAILED;
    }
    choice_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a26();
    }
    return $r1;
  }
  private function parseimage($silence, $boolParams) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "[[", $this->currPos, 2, false) === 0) {
      $r4 = "[[";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r5 = $this->discardanySpacing($silence);
    if ($r5===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "image:", $this->currPos, 6, true) === 0) {
      $r6 = substr($this->input, $this->currPos, 6);
      $this->currPos += 6;
    } else {
      if (!$silence) {$this->fail(17);}
      $r6 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r7 = $this->discardanySpacing($silence);
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p9 = $this->currPos;
    $r8 = $this->discardinlineText($silence, $boolParams | 0x2);
    // imageFileName <- $r8
    if ($r8!==self::$FAILED) {
      $r8 = substr($this->input, $p9, $this->currPos - $p9);
    } else {
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p9
    $p9 = $this->currPos;
    // start seq_2
    $p11 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r12 = "|";
    } else {
      if (!$silence) {$this->fail(7);}
      $r12 = self::$FAILED;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r13 = $this->discardanySpacing($silence);
    if ($r13===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r14 = $this->parsenumber($silence);
    // width <- $r14
    if ($r14===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r15 = $this->discardanySpacing($silence);
    if ($r15===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    if (($this->input[$this->currPos] ?? null) === ",") {
      $this->currPos++;
      $r16 = ",";
    } else {
      if (!$silence) {$this->fail(18);}
      $r16 = self::$FAILED;
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r17 = $this->discardanySpacing($silence);
    if ($r17===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r18 = $this->parsenumber($silence);
    // height <- $r18
    if ($r18===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r19 = $this->discardanySpacing($silence);
    if ($r19===self::$FAILED) {
      $this->currPos = $p11;
      $r10 = self::$FAILED;
      goto seq_2;
    }
    $r10 = true;
    seq_2:
    if ($r10!==self::$FAILED) {
      $this->savedPos = $p9;
      $r10 = $this->a27($r8, $r14, $r18);
    } else {
      $r10 = null;
    }
    // free $p11
    // dimensions <- $r10
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "]]", $this->currPos, 2, false) === 0) {
      $r20 = "]]";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(19);}
      $r20 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a28($r8, $r10);
    }
    // free $p3
    return $r1;
  }
  private function parsewikilink($silence, $boolParams) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "[[", $this->currPos, 2, false) === 0) {
      $r4 = "[[";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(16);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p6 = $this->currPos;
    $r5 = $this->discardinlineText($silence, $boolParams | 0x2);
    // target <- $r5
    if ($r5!==self::$FAILED) {
      $r5 = substr($this->input, $p6, $this->currPos - $p6);
    } else {
      $r5 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p6
    $p6 = $this->currPos;
    // start seq_2
    $p8 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "|") {
      $this->currPos++;
      $r9 = "|";
    } else {
      if (!$silence) {$this->fail(7);}
      $r9 = self::$FAILED;
      $r7 = self::$FAILED;
      goto seq_2;
    }
    $p11 = $this->currPos;
    $r10 = $this->discardinlineText($silence, $boolParams | 0x2);
    // text <- $r10
    if ($r10!==self::$FAILED) {
      $r10 = substr($this->input, $p11, $this->currPos - $p11);
    } else {
      $r10 = self::$FAILED;
      $this->currPos = $p8;
      $r7 = self::$FAILED;
      goto seq_2;
    }
    // free $p11
    $r7 = true;
    seq_2:
    if ($r7!==self::$FAILED) {
      $this->savedPos = $p6;
      $r7 = $this->a29($r5, $r10);
    } else {
      $r7 = null;
    }
    // free $p8
    // displayText <- $r7
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "]]", $this->currPos, 2, false) === 0) {
      $r12 = "]]";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(19);}
      $r12 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a30($r5, $r7);
    }
    // free $p3
    return $r1;
  }
  private function parsebold($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r4 = "'''";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(20);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p5 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "'") {
      $this->currPos++;
      $r6 = "'";
    } else {
      $r6 = self::$FAILED;
    }
    if ($r6 === self::$FAILED) {
      $r6 = false;
    } else {
      $r6 = self::$FAILED;
      $this->currPos = $p5;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r7 = $this->parseinlineLine($silence, 0x4);
    // content <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "'''", $this->currPos, 3, false) === 0) {
      $r8 = "'''";
      $this->currPos += 3;
    } else {
      if (!$silence) {$this->fail(20);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p5 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "'") {
      $this->currPos++;
      $r9 = "'";
    } else {
      $r9 = self::$FAILED;
    }
    if ($r9 === self::$FAILED) {
      $r9 = false;
    } else {
      $r9 = self::$FAILED;
      $this->currPos = $p5;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a31($r7);
    }
    // free $p3
    return $r1;
  }
  private function parseitalics($silence) {
    $p2 = $this->currPos;
    // start seq_1
    $p3 = $this->currPos;
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r4 = "''";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(21);}
      $r4 = self::$FAILED;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $p5 = $this->currPos;
    if (($this->input[$this->currPos] ?? null) === "'") {
      $this->currPos++;
      $r6 = "'";
    } else {
      $r6 = self::$FAILED;
    }
    if ($r6 === self::$FAILED) {
      $r6 = false;
    } else {
      $r6 = self::$FAILED;
      $this->currPos = $p5;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    // free $p5
    $r7 = $this->parseinlineLine($silence, 0x8);
    // content <- $r7
    if ($r7===self::$FAILED) {
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    if ($this->currPos >= $this->inputLength ? false : substr_compare($this->input, "''", $this->currPos, 2, false) === 0) {
      $r8 = "''";
      $this->currPos += 2;
    } else {
      if (!$silence) {$this->fail(21);}
      $r8 = self::$FAILED;
      $this->currPos = $p3;
      $r1 = self::$FAILED;
      goto seq_1;
    }
    $r1 = true;
    seq_1:
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a32($r7);
    }
    // free $p3
    return $r1;
  }
  private function parsepermission($silence) {
    $p1 = $this->currPos;
    $r2 = $this->discardinlineText($silence, 0x10);
    if ($r2!==self::$FAILED) {
      $r2 = substr($this->input, $p1, $this->currPos - $p1);
    } else {
      $r2 = self::$FAILED;
    }
    // free $p1
    return $r2;
  }
  private function parsenumber($silence) {
    $p2 = $this->currPos;
    $p4 = $this->currPos;
    $r3 = self::$FAILED;
    for (;;) {
      $r5 = $this->input[$this->currPos] ?? '';
      if (preg_match("/^[0-9]/", $r5)) {
        $this->currPos++;
        $r3 = true;
      } else {
        $r5 = self::$FAILED;
        if (!$silence) {$this->fail(22);}
        break;
      }
    }
    // numberString <- $r3
    if ($r3!==self::$FAILED) {
      $r3 = substr($this->input, $p4, $this->currPos - $p4);
    } else {
      $r3 = self::$FAILED;
    }
    // free $r5
    // free $p4
    $r1 = $r3;
    if ($r1!==self::$FAILED) {
      $this->savedPos = $p2;
      $r1 = $this->a33($r3);
    }
    return $r1;
  }

  public function parse($input, $options = []) {
    $this->initInternal($input, $options);
    $startRule = $options['startRule'] ?? '(DEFAULT)';
    $result = null;

    if (!empty($options['stream'])) {
      switch ($startRule) {
        
        default:
          throw new \WikiPEG\InternalError("Can't stream rule $startRule.");
      }
    } else {
      switch ($startRule) {
        case '(DEFAULT)':
        case "start":
          $result = $this->parsestart(false);
          break;
        default:
          throw new \WikiPEG\InternalError("Can't start parsing from rule $startRule.");
      }
    }

    if ($result !== self::$FAILED && $this->currPos === $this->inputLength) {
      return $result;
    } else {
      if ($result !== self::$FAILED && $this->currPos < $this->inputLength) {
        $this->fail(0);
      }
      throw $this->buildParseException();
    }
  }
}

