import React, { createContext, useReducer } from 'react';

// Import globalsData variable from within script in the HTML.
import globalsData from 'globalsData';

export const GlobalStateContext = createContext(undefined);
export const GlobalStateDispatchContext = createContext(undefined);

export const SET_QUICK_NAVIGATOR_OPEN = 'SET_QUICK_NAVIGATOR_OPEN';

export const GlobalStateReducer = ( state, action ) => {
	const { type, payload } = action;
	switch ( type )
	{
		case SET_QUICK_NAVIGATOR_OPEN: {
			return {
				...state,
				QuickNavigationOpen: payload
			};
		}

		default: {
			return state;
		}
	}
};

export const GlobalStateProvider = ({ initialState, dispatch, children }) => {
	return (
		<GlobalStateContext.Provider value={ { ...globalsData, ...initialState } }>
			<GlobalStateDispatchContext.Provider value={ dispatch }>
				{ children }
			</GlobalStateDispatchContext.Provider>
		</GlobalStateContext.Provider>
	);
}

const initialState = {
  QuickNavigatorOpen: false
};

export const GlobalStateWrapper = ({ children }) =>
{
  const [ state, dispatch ] = useReducer( GlobalStateReducer, initialState );
  return (
    <GlobalStateProvider initialState={ state } dispatch={ dispatch }>
      { children }
    </GlobalStateProvider>
  );
}
