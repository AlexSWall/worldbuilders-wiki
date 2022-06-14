import React, { ReactElement } from 'react';

import { Item } from './Item';

interface Props
{
	text: string;
	href?: string | null;
	type?: 'navbar' | 'dropdown';
	children?: React.ReactNode | null;
};

export const HrefItem = ({ text, href=null, type='navbar', children=null }: Props): ReactElement =>
{
	if ( href === null )
	{
		href = '/#' + text.replace(/[^a-zA-Z]+/g, '-').toLowerCase();
	}

	return (
		<Item
			text={ text }
			type={ type }
			action={ { href: href } }
			children={ children }
		/>
	);
};
