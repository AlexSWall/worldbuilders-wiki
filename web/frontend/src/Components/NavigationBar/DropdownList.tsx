import React, { ReactElement } from 'react';

interface Props
{
	children: React.ReactNode;
};

export const DropdownList = ({ children }: Props): ReactElement =>
{
	return (
		<ul className="dropdown-list">
			{children}
		</ul>
	);
};
