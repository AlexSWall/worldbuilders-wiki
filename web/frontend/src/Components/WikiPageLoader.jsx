import React, { useState, useEffect } from 'react';

import WikiPanel from './WikiPanel';

import { makeApiGetRequest }  from 'utils/api';

export default function WikiPageLoader({ urlBase })
{
	const [wikiPageData, setWikiPageData] = useState({});
	const [pagePositions, setPagePosition] = useState({});

	const fetchAndUpdatePageContents = ( wikiPagePath, heading ) =>
	{
		makeApiGetRequest(
			'/w/' + wikiPagePath,
			( res, data ) => data.wikiPage !== null,
			( res, data ) => {
				// -- Success callback --

				// We know this is non-null by passing success predicate
				const wikiPageData = data.wikiPage;

				// Set tab title to the title of the wikipage.
				document.title = wikiPageData.title;

				// Set wikipage data state.
				setWikiPageData( wikiPageData );

				// Move to heading, if there was one.
				if ( heading && heading !== '' )
				{
					const headingElement = document.getElementById(heading);

					// If we've found the heading element, scroll it into view
					if ( headingElement !== null )
					{
						headingElement.scrollIntoView();
					}
				}
			},
			null,  // No error callback
			true  // Allow 404
		);
	};

	// Run after initial mounting of the component...
	useEffect(() =>
		{
			window.addEventListener("hashchange", (_event) =>
				{
					const hash = window.location.hash.substring(1);
					const [wikiPagePath, heading] = hash.split('#');

					fetchAndUpdatePageContents(wikiPagePath, heading);
				}
			);

			// Load the content by firing a 'hashchange'.
			window.dispatchEvent(new HashChangeEvent("hashchange"));
		}, []);

	return (Object.keys(wikiPageData).length === 0)
		? ( <i> Fetching and loading content...  </i> )
		: ( <WikiPanel {...wikiPageData} /> );
}
