import React, { useState, useEffect } from 'react';

import WikiPanel from './WikiPanel';

export default function WikiPageLoader({ urlBase }) 
{
	const [wikiPageData, setWikiPageData] = useState({});

	// -- Start of 'member' functions --

	const getAndUpdatePageContents = (wikiPagePath, heading) =>
	{
		fetch(urlBase + wikiPagePath)
			.then(res => res.json())
			.then(response => {
				const wikiPageData = response.wikiPage;

				// Set title.
				document.title = wikiPageData.title;

				// Set inner React component and its data.
				setWikiPageData(wikiPageData);

				// Move to heading, if there was one.
				moveToHeading(heading);
			});
	};

	const moveToHeading = (heading) =>
	{
		if ( heading === '' )
			return;

		const headingElement = document.getElementById(heading);

		if ( headingElement !== null )
			headingElement.scrollIntoView();
	}

	// -- End of 'member' functions --

	// Run after initial mounting of the component...
	useEffect(() =>
		{
			window.addEventListener("hashchange", (_event) =>
				{
					const hash = window.location.hash.substring(1);
					const [wikiPagePath, heading] = hash.split('#');

					if ( wikiPagePath === '' )
						// If there is no hash, set it to 'Home'.
						// This will result in function being called again.
						window.location.hash = 'Home';
					else
						// Otherwise, update the contents by fetching the intended contents,
						// setting the inner component for it, and moving to the heading.
						getAndUpdatePageContents(wikiPagePath, heading);
				}
			);

			// Load the content by firing a 'hashchange'.
			window.dispatchEvent(new HashChangeEvent("hashchange"));
		}, []);

	return (Object.keys(wikiPageData).length === 0)
		? ( <i> Fetching and loading content...  </i> )
		: ( <WikiPanel {...wikiPageData} /> );
}
