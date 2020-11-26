import React, { createContext } from 'react';

const GlobalsContext = createContext();

const GlobalsProvider = ({ globals, children }) =>
{
	return (
		<GlobalsContext.Provider value={ globals }>
			{ children }
		</GlobalsContext.Provider>
	);
}

export default GlobalsContext;
export { GlobalsProvider };
