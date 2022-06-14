import React, { ReactElement } from 'react';

interface Props
{
	title: string;
	children?: React.ReactNode | null;
};

export const Card = ({ title, children = null }: Props): ReactElement =>
{
	return (
		<div className='card'>
			<div className='card-header'>
				{ title }
			</div>
			<div className='card-body'>
				{ children }
			</div>
		</div>
	);
};
