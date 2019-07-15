<?php declare( strict_types = 1 );

namespace App\Permissions;

use App\Utilities\ArrayBasedSet;

class PermissionsUtilities
{
	public static function getViewableBlocks( $permissions, array $wikiPagePermissionBlocks ): array
	{
		$viewableBlocks = array();
		foreach ( $wikiPagePermissionBlocks as $block )
			if ( self::satisfiesPermissionExpression( $permissions, $block->getPermissionsExpression() ) )
				$viewableBlocks[] = $block;
		return $viewableBlocks;
	}

	public static function satisfiesPermissionExpression( $permissions, string $permissionsExpression ): bool
	{
		if ( $permissionsExpression === '' )
			return true;

		if ( is_null($permissions) )
			$permissions = new ArrayBasedSet();

		$tokens = explode(' ', $permissionsExpression);

		$stack = array();
		foreach ( $tokens as $token )
		{
			switch ( $token )
			{
				case '&&':
					$left = strtolower(array_pop($stack));
					$right = strtolower(array_pop($stack));

					if ( $left !== 'true' && $left !== 'false' )
						$left = $permissions->has($left);

					if ( $right !== 'true' && $right !== 'false' )
						$right = $permissions->has($right);

					if ( $left && $right )
						array_push($stack, 'true');
					else
						array_push($stack, 'false');
					break;
				case '||':
					$left = strtolower(array_pop($stack));
					$right = strtolower(array_pop($stack));

					if ( $left !== 'true' && $left !== 'false' )
						$left = $permissions->has($left);

					if ( $right !== 'true' && $right !== 'false' )
						$right = $permissions->has($right);

					if ( $left || $right )
						array_push($stack, 'true');
					else
						array_push($stack, 'false');

					break;
				default:
					array_push($stack, $token);
			}
		}

		if ( sizeof($stack) === 1 && $stack[0] === 'true' )
			return true;
		else
			return false;
	}
}