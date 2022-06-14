import React, { ReactElement } from 'react';

import { Item } from './Item';

interface Props
{
	text: string;
	onClick: () => void;
	type?: 'navbar' | 'dropdown';
	children?: React.ReactNode;
};

export const OnClickItem = ({ text, onClick, type='navbar', children }: Props): ReactElement =>
{
	return (
		<Item
			text={ text }
			type={ type }
			action={ { onClick: onClick } }
			children={ children }
		/>
	);
};
