import React from 'react';

import { getWeakPasswordString } from 'utils/crypto';

export default function WeakPasswordWarning({ password, width })
{
	const weakPasswordString = getWeakPasswordString(password);

	if ( weakPasswordString == '' )
		return null;

	return (
		<label className='form-warning' style={ { 'width': width } }>
			{ weakPasswordString }
		</label>
	);
}
