import { useEffect, useRef } from 'react';

export default function useDelayedEffect(effectFn: () => void, dependencies: any[] = []): void
{
	const firstUpdate = useRef<boolean>( true );

	useEffect(() => {
		if (firstUpdate.current)
		{
			firstUpdate.current = false;
			return;
		}

		effectFn();
	}, dependencies);
}
