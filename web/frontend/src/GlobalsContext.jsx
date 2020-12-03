import React, { createContext } from 'react';

// Import globalsData variable from within script in the HTML.
import globalsData from 'globalsData';

const GlobalsContext = createContext();

const GlobalsProvider = ({ extraGlobals={}, children }) =>
{
	return (
		<GlobalsContext.Provider value={ { ...globalsData, ...extraGlobals } }>
			{ children }
		</GlobalsContext.Provider>
	);
}

export default GlobalsContext;
export { GlobalsProvider };
