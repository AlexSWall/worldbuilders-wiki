import React, { ReactElement } from 'react';

import { getWeakPasswordString } from 'utils/crypto';

interface Props
{
	password: string;
	width: number;
};

export const WeakPasswordWarning = ({ password, width }: Props): ReactElement | null =>
{
	const weakPasswordString = getWeakPasswordString(password);

	if ( weakPasswordString == '' )
		return null;

	return (
		<label className='form-warning' style={ { 'width': width } }>
			{ weakPasswordString }
		</label>
	);
};
