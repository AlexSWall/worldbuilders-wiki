import React, { createContext } from 'react';

// Import webpageBaseData variable from wiki.index.twig
import webpageBaseData from 'webpageBaseData';

const globals = {
	authData: webpageBaseData.authenticationData,
	flash: webpageBaseData.flash
};

const GlobalsContext = createContext();

const GlobalsProvider = ({ extraGlobals={}, children }) =>
{
	return (
		<GlobalsContext.Provider value={ { ...globals, ...extraGlobals } }>
			{ children }
		</GlobalsContext.Provider>
	);
}

export default GlobalsContext;
export { GlobalsProvider };
