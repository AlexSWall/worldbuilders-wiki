import React, { ReactElement } from 'react';

import { Item } from './Item';

interface Props
{
	text: string;
	type?: 'navbar' | 'dropdown';
	children: React.ReactNode;
};

export const NullItem = ({ text, type='navbar', children }: Props): ReactElement =>
{
	return (
		<Item
			text={ text }
			type={ type }
			action={ { onClick: () => { return false; } } }
			children={ children }
		/>
	);
};
