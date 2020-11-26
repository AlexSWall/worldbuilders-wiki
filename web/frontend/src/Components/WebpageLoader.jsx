import React, { useState, useEffect } from 'react';

export default function WebpageLoader({ urlBase, componentMapper }) 
{
	const [ChildComponent, setChild] = useState(undefined);
	const [webpageData, setWebpageData] = useState({});

	// -- Start of 'member' functions --

	const getAndUpdatePageContents = (webpagePath, heading) =>
	{
		console.log('Fetching ' + urlBase + webpagePath);
		fetch(urlBase + webpagePath)
			.then(res => res.json())
			.then(response => {
				const webpageData = response.wikiPage;

				// Set title.
				document.title = webpageData.title;

				// Set inner React component and its data.
				setWebpageData(webpageData);
				// Determining that I had to use () => in the line below took far
				// too long. WHY DO YOU DO THIS REACT.
				setChild(() => componentMapper(webpageData.urlPath));

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
					const [webpagePath, heading] = hash.split('#');

					console.log('Hash changed to ' + hash + '.');

					if ( webpagePath === '' )
						// If there is no hash, set it to 'Home'.
						// This will result in function being called again.
						window.location.hash = 'Home';
					else
						// Otherwise, update the contents by fetching the intended contents,
						// setting the inner component for it, and moving to the heading.
						getAndUpdatePageContents(webpagePath, heading);
				}
			);

			// Load the content by firing a 'hashchange'.
			window.dispatchEvent(new HashChangeEvent("hashchange"));
		}, []);

	return (ChildComponent === undefined)
		? ( <i> Fetching and loading content...  </i> )
		: ( <ChildComponent {...webpageData} /> );
}
