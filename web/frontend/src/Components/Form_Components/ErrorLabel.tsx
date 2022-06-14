import React, { ReactElement } from 'react';

interface Props
{
	width: number;
	children: React.ReactNode;
};

export const ErrorLabel = ({ width, children }: Props): ReactElement =>
{
	return (
		<div className='form-group'>
			<label className='form-label-error' style={ { width: width } }>
				{ children }
			</label>
		</div>
	);
};
