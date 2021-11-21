import { useEffect, useRef } from 'react';

export default function useDelayedEffect(effectFn, dependencies = [])
{
	const firstUpdate = useRef(true);
	useEffect(() => {
		if (firstUpdate.current) {
			firstUpdate.current = false;
			return;
		}

		effectFn();
	}, dependencies);
}
