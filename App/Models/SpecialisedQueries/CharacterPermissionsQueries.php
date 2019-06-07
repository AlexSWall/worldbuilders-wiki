<?php

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;

use App\Utilities\ArrayBasedSet;

class CharacterPermissionsQueries
{
	private static function getCharacterPermissionRelationsTable()
	{
		return ;
	}

	/**
	 *
	 *
	 */
	public static function removeCharacterPermissions($characterId, $permissions)
	{
		if ( count($permissions) == 0 )
			return;

		DB::table('CharacterPermissionRelations')
				->where('characterId', $characterId)
				->whereIn($permissions)
	}

	/**
	 * SELECT PermissionName
	 *     FROM WikiPermissions
	 *     INNER JOIN CharacterPermissionRelations
	 *         ON WikiPermissions.PermissionId = CharacterPermissionRelations.PermissionId
	 *     WHERE CharacterPermissionRelations.CharacterId = {$CharacterId};
	 */
	public static function runCharacterIdToPermissionNamesQuery($characterId)
	{
		$permissionsArray = DB::table('WikiPermissions')
				->select('PermissionName')
				->join('CharacterPermissionRelations', 'WikiPermissions.PermissionId', '=', 'CharacterPermissionRelations.PermissionId')
				->where('CharacterPermissionRelations.CharacterId', $characterId)
				->all();
		$permissionsSet = new ArrayBasedSet($permissionsArray);
		return $permissionsSet;
	}
}

