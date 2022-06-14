import React, { ReactElement } from 'react';

interface Props
{
	sidebar: 'QuickNavigator' | null
};

export const Sidebar = ({ sidebar }: Props): ReactElement | null =>
{
	switch ( sidebar )
	{
		case 'QuickNavigator':
			return (
				<aside className='sidebar' />
			);

		case null:
			return null;
	}
};
