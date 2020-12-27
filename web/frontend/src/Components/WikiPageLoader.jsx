import React, { useState, useEffect } from 'react';

import WikiPanel from './WikiPanel';

export default function WikiPageLoader({ urlBase }) 
{
	const [wikiPageData, setWikiPageData] = useState({});

	// -- Start of 'member' functions --

	const fetchAndUpdatePageContents = (wikiPagePath, heading) =>
	{
		fetch(urlBase + wikiPagePath)
			.then(async response => {

				if (!response.ok)
					console.log('Error: Received status code ' + response.status + ' in response to POST request');

				// Ensure data is JSON
				const contentType = response.headers.get("content-type");
				if (!contentType || contentType.indexOf("application/json") === -1)
				{
					try
					{
						data = await response.text();
						console.log('Error (text): ' + text);
					}
					catch(e)
					{
						console.log('Error, and then error on handling: ' + e);
					}
					return
				}

				// Ensure JSON data parses
				let data;
				try {
					data = await response.json();
				} catch(e) {
					console.log('Error response, and then JSON in body failed to parse: ' + e);
					return;
				}

				const wikiPageData = data.wikiPage;

				// Ensure JSON has wikiPageData key
				if (wikiPageData === null)
				{
					if (data.error)
						console.log('Error response: ' + data.error);
					else
						console.log('Error response and JSON has no error key: ' + e);
					return;
				}

				// -- Successfully got data --

				// Set title.
				document.title = wikiPageData.title;

				// Set inner React component and its data.
				setWikiPageData(wikiPageData);

				// Move to heading, if there was one.
				moveToHeading(heading);

			}).catch( error => {
				console.log('Failed to make POST request...')
				console.log(error);
			});
	};

	const moveToHeading = (heading) =>
	{
		if ( !heading || heading === '' )
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
