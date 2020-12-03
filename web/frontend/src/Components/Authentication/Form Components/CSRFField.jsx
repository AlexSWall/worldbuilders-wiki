import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

export default function CSRFField()
{
	const globals = useContext(GlobalsContext);

	return (
		<div dangerouslySetInnerHTML={ { __html: globals.csrfHTML } } />
	);
}
