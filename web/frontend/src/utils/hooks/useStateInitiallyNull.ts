import { useState } from 'react';

export type SetStateActionInitiallyNull<T> = T | ( ( prevState: T | null ) => T );

export default function useStateInitiallyNull<T>():
	[ T | null, React.Dispatch<SetStateActionInitiallyNull<T>> ]
{
	const [ state, setStateOrNull ] = useState<T | null>( null );

	const setState = (
		newStateOrFunc: SetStateActionInitiallyNull<T>
	) => setStateOrNull(
		newStateOrFunc instanceof Function
			? newStateOrFunc( state )
			: newStateOrFunc
	);

	return [ state, setState ];
}
