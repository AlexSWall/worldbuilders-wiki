import { assertNotEmpty } from "./types";


export function getWikiPagePathAndHeading( hash: string ): [string, string | null]
{
	// Strip trailing hash and split around the next hash.
	const [ wikiPagePath, heading ] = hash.substring(1).split('#');
	assertNotEmpty( wikiPagePath );
	return [ wikiPagePath, heading !== undefined ? heading : null ];
}
