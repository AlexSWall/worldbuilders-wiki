import React, { createContext } from 'react';

// // Allows one assignment, via a SET_GLOBALS action, and then cannot be modified.
// const SetOnceReducer = (state, action) => {
// 	console.log('Attempting to set global state...')
// 	console.log('Current state:')
// 	console.log(state);

// 	if ( Object.keys(state).length !== 0)
// 	{
// 		console.log('Global state already set')
// 		return state;
// 	}

// 	console.log('Global state not yet set, setting global state with action:')
// 	console.log(action);

// 	switch(action.type)
// 	{
// 		case 'SET_GLOBALS':
// 			console.log('SET_GLOBALS')
// 			return {...action.state};

// 		default:
// 			console.log('default action')
// 			return state;
// 	}
// }

// const initialState = {};

// export const GlobalContext = createContext(initialState);

// const GlobalStore = ({ data, children }) =>
// {
// 	const [state,] = useReducer(SetOnceReducer, data);

// 	return (
// 		<GlobalContext.Provider state={ state }>
// 			{ children }
// 		</GlobalContext.Provider>
// 	);
// }

// export default GlobalStore;

const { Provider, Consumer } = createContext();

const GlobalProvider = ({ data, children }) =>
{
	// const [state,] = useState(data);

	return (
		<Provider value={ data }>
			{ children }
		</Provider>
	);
}

export { GlobalProvider, Consumer as GlobalConsumer };
