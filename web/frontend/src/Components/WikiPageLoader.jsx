import React, { useState, useEffect } from 'react';

import WikiPanel from './WikiPanel';

import useStateWithGetter  from 'utils/useStateWithGetter';
import useReducerWithGetter  from 'utils/useReducerWithGetter';

import { makeApiGetRequest }  from 'utils/api';


/**
 * WikiPageLoader deals with the loading of, and setting the content of, a
 * WikiPanel, and then the setting the initial window y-coordinate to either the
 * header contained in the wikipage path or to the previous page position the
 * client was at for the wikipage.
 * It also deals with caching wikipage data and loading it on cache hits, before
 * fetching the most up-to-date version of the date to ensure the version of the
 * page being presented is not old.
 *
 * Some complexity stems from dealing with the bfcache, which sets the window's
 * y-coordinate before the hashchange event fires, the listener for which loads
 * the page and then sets the new y-coordinate (which must be done after as we
 * cannot set the y-coordinate before loading the content).
 *
 * As we're looking to remember the y-corordinates of where we last left off for
 * each wikipage, this means that we can't access this to set it in the
 * hashchange event, and therefore need to do it in a popstate event listener,
 * which can access the y-coordinate of the window before unloading.
 *
 * Therefore, to use it in the hashchange event listener, we need to save it off
 * in the popstate event listener, therefore using the useState hook, and
 * setting the state on each unload. However, we then need to load this state in
 * the hashchange event listener, which will have captured in its closure only
 * the original value of the page's position and will not get the up-to-date
 * value that is set with the useState's setter.
 *
 * Therefore, we need to get access to the state not via the closure but via a
 * getter (captured in the closure). To do this, we use the custom
 * useStateWithGetter hook.
 *
 * Finally, the initial loading of the page does not come with an unload, so we
 * need to set the wikipage hash at the beginning of an onMount useEffect.
 */
export default function WikiPageLoader({ urlBase })
{
	// == State ==

	// Used for setting the page positions cache when unloading a page, to know
	// which page to save the position for.
	const [, getCurrentPage, setCurrentPage] = useStateWithGetter(null);

	// Stores the wikipage data retrieved from the server.
	const [wikiPageData, setWikiPageData] = useState({});

	// Stores the initial location for the current page..
	const [initialLocation, setInitialLocation] = useState(undefined);

	// A cache of the wikipage data for all wikipages visited in this javascript
	// session.
	const [, getPageContent, savePageContent] = useReducerWithGetter((state, content) =>
	{
			// Merge position key-value pair into state.
			return { ...state, ...content };
	}, {});

	// A cache of all positions for wikipages visited in this javascript session.
	const [, getPagePositions, savePagePosition] = useReducerWithGetter((state, position) =>
	{
			// Merge position key-value pair into state.
			return { ...state, ...position };
	}, {});


	// == useEffects ==

	// Run after initial mounting of the component (only) to add event listeners.
	useEffect(() =>
	{
		// Set the current page hash initially (to be handled by the popstate
		// listener in the future)
		const initialHash = window.location.hash.substring(1);
		setCurrentPage(initialHash);

		// Define popstate listener function
		const onPopState = (event) =>
		{
			// Set the y-coordinate for the page we're leaving to be the
			// y-coordinate that we're currently at, as we're unloading it.
			savePagePosition({ [getCurrentPage()]: window.pageYOffset });

			// Check whether we've cached the page; if we have, load it while
			// we wait for the fetch to ensure we have the most up to date
			// version.
			const newHash = event.target.location.hash.substring(1);
			const [newWikiPage, newHeading] = newHash.split('#');
			const pageContentCacheLookup = getPageContent()[newWikiPage];
			if ( pageContentCacheLookup )
			{
				// Got cache hit; set page data from cache
				setWikiPageData(pageContentCacheLookup);

				// Get the initial location for the page and set the
				// initialLocation state, but then also immediately call the
				// changeWindowYCoordinate to ensure we don't wait on the
				// hashchange event fetch to finish before re-rendering and
				// running our useEffects.
				const initialLocation = getPagePositions()[newWikiPage];
				setInitialLocation(initialLocation);
				changeWindowYCoordinate(initialLocation);
			}
		}

		// Define hashchange listener function
		const onHashChange = (event) =>
		{
			// We use window.location for the new hash as we're not guaranteed
			// to have event.newURL set when throwing the event ourselves.
			const newHash = window.location.hash.substring(1);

			// Extract heading
			const [newWikiPage, newHeading] = newHash.split('#');

			// Set our current page to be the new page we're visiting, so that
			// this popstate listener knows which page to set the position for
			// on the next call.
			setCurrentPage(newWikiPage);

			if ( newHeading !== undefined )
			{
				setInitialLocation(newHeading);
			}
			else
			{
				// We have no heading, so set the initial location to be the
				// position we were at when we were last on this page last (if
				// ever).
				setInitialLocation(getPagePositions()[newWikiPage]);
			}

			fetchAndUpdatePageContents(newWikiPage, setWikiPageData, savePageContent);
		}

		// The popstate event is fired each time when the current history entry
		// changes.
		window.addEventListener('popstate', onPopState, false);

		window.addEventListener("hashchange", onHashChange );

		// Load the content by firing a 'hashchange'.
		window.dispatchEvent(new HashChangeEvent("hashchange"));

		// Return a clean-up function on dismount; this removes our event
		// listener.
		return () => {
			window.removeEventListener('hashchange', onHashChange);
			window.removeEventListener('popstate', onPopState);
		}
	}, []);

	// Run after our wikiPageData state updates, to ensure we move to the right
	// location in the page for it (after the render).
	useEffect( () =>
	{
		changeWindowYCoordinate(initialLocation);
	}, [wikiPageData] );


	// == Render ==
	//
	return (Object.keys(wikiPageData).length === 0)
		? ( <i> Fetching and loading content...  </i> )
		: ( <WikiPanel {...wikiPageData} /> );
}

function fetchAndUpdatePageContents( wikiPagePath, setWikiPageData, savePageContent )
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
			savePageContent( { [wikiPagePath]: wikiPageData } );
		},
		null,  // No error callback
		true  // Allow 404
	);
};

function changeWindowYCoordinate( initialLocation )
{
	// Move to heading, if there was one.
	if ( typeof initialLocation === 'string' && initialLocation !== '' )
	{
		// Our location is a string, so it's a heading.
		const heading = initialLocation;

		const headingElement = document.getElementById(heading);

		// If we've found the heading element, scroll it into view
		if ( headingElement !== null )
		{
			headingElement.scrollIntoView();
		}
	}
	else if ( typeof initialLocation === 'number' )
	{
		// Our location is a number, so it's a y-coordinate
		const yCoord = initialLocation;

		window.scrollTo(0, yCoord);
	}
	else
	{
		// typeof location == 'undefined'
		window.scrollTo(0, 0);
	}
	// ...else location is undefined, in which case this is a page we
	// haven't been to yet.
}
