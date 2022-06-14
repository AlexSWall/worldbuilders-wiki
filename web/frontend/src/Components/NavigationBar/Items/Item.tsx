import React, { ReactElement } from 'react';

interface Props
{
	text: string;
	type: 'navbar' | 'dropdown';
	action: { href: string } | { onClick: () => void };
	children: React.ReactNode;
	extraComponent?: ReactElement;
};

export const Item = ({ text, type, action, children, extraComponent=undefined }: Props): ReactElement =>
{
	return (
		<li className={ type + '-item' }>
			<a className={ type + '-link underline-right' }
			{ ...action }>
				{ text }
			</a>
			{ extraComponent }
			{ children }
		</li>
	);
};
