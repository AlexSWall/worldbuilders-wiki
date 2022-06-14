import React, { ReactElement } from 'react';

interface Props
{
	disabled: boolean;
	children: React.ReactNode;
};

export const SubmitButton = ({ disabled, children }: Props): ReactElement =>
{
	return (
		<button
			type='submit'
			className='form-submit'
			disabled={ disabled }
		>
			{ children ? children : 'Submit' }
		</button>
	);
};
