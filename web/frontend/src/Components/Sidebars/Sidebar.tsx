import React, { ReactElement } from 'react';
import QuickNavigatorController from './QuickNavigator/QuickNavigatorController';

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
				<aside className='sidebar'>
					<QuickNavigatorController />
				</aside>
			);

		case null:
			return null;
	}
};
