import { useEffect, useReducer, useRef } from 'react';

export default function useReducerWithGetter(reducer, initialValue)
{
	const [value, setValue] = useReducer(reducer, initialValue);

	// Create a reference to be passed off to a closure, which will point to the
	// value we set here.
	const valueRef = useRef(value);

	// Update the value that the reference points to whenever the value changes.
	useEffect( () => {
		valueRef.current = value;
	}, [value]);

	// Create a getter which captured the value reference by closure and simply
	// gets its current value.
	const getValue = () => { return valueRef.current; };

	return [value, getValue, setValue];
}
