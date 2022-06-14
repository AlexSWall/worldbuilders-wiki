import React, { createContext, ReactElement, useReducer } from 'react';


/* ===== Global State Structure ===== */

// Import globalsData variable from within script in the HTML.
// @ts-ignore
import globalsData from 'globalsData';

type Panel = 'QuickNavigator' | null

type GlobalState = {
	isAuthenticated: boolean;
	preferredName: string;
	csrfTokens: {
		"csrf_name": string;
		"csrf_value": string;
	};
	leftSidebar: Panel;
	rightSidebar: Panel;
}

export type CsrfTokens = GlobalState['csrfTokens'];

const InitialGlobalState: GlobalState =
{
	// Initial globals.
	isAuthenticated: globalsData.isAuthenticated,
	preferredName: globalsData.preferredName,
	csrfTokens: globalsData.csrfTokens,

	// Frontnend globals.
	leftSidebar: null,
	rightSidebar: null
};


/* ===== Global State Reducer Structure ===== */

export const SET_LEFT_SIDEBAR = 'SET_LEFT_SIDEBAR';
export const SET_RIGHT_SIDEBAR = 'SET_RIGHT_SIDEBAR';

type GlobalStateReducerAction =
	| { type: 'SET_LEFT_SIDEBAR'; panel: Panel; }
	| { type: 'SET_RIGHT_SIDEBAR'; panel: Panel; }

export const GlobalStateReducer = ( state: GlobalState, action: GlobalStateReducerAction ): GlobalState => {
	switch ( action.type )
	{
		case SET_LEFT_SIDEBAR: {
			return {
				...state,
				leftSidebar: action.panel
			};
		}

		case SET_RIGHT_SIDEBAR: {
			return {
				...state,
				rightSidebar: action.panel
			};
		}

		default: {
			return state;
		}
	}
};


/* ===== Global State Contexts ===== */

export const GlobalStateContext = createContext<GlobalState>( InitialGlobalState );
export const GlobalStateDispatchContext = createContext<React.Dispatch<GlobalStateReducerAction>>(
	(() => null) as React.Dispatch<GlobalStateReducerAction>
);


/* ===== Global State Providers ===== */

interface GlobalStateProviderProps
{
	state: GlobalState;
	dispatch: React.Dispatch<GlobalStateReducerAction>;
	children: React.ReactNode;
};

export const GlobalStateProvider = ({ state, dispatch, children }: GlobalStateProviderProps): ReactElement =>
{
	return (
		<GlobalStateContext.Provider value={ state }>
			<GlobalStateDispatchContext.Provider value={ dispatch }>
				{ children }
			</GlobalStateDispatchContext.Provider>
		</GlobalStateContext.Provider>
	);
}

interface GlobalStateWrapperProps
{
	children: React.ReactNode;
};

export const GlobalStateWrapper = ({ children }: GlobalStateWrapperProps): ReactElement =>
{
  const [ state, dispatch ] = useReducer( GlobalStateReducer, InitialGlobalState );

  return (
    <GlobalStateProvider state={ state } dispatch={ dispatch }>
      { children }
    </GlobalStateProvider>
  );
}
