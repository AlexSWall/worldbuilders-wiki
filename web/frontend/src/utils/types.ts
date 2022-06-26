export function RemoveNullFromStateSetter<T>(
	setState: React.Dispatch<React.SetStateAction<T | null>>
): React.Dispatch<T>
{
	return ( value: T ) => setState( value );
}

/**
 * Asserts that a value is not null nor undefined.
 */
export function assertNotEmpty<T>( value: T | null | undefined ): asserts value is T
{
	switch ( value )
	{
		case null:
			throw new Error('Assertion that the provided value is not null failed.');

		case undefined:
			throw new Error('Assertion that the provided value is not undefined failed.');
	}
}

/**
 * Returns the value when it's neither null nor undefined, and throws if it is
 * null or undefined.
 */
export function returnNotEmpty<T>( value: T | null | undefined ): T
{
	assertNotEmpty(value);
	return value;
}
