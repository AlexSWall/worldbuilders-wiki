import React from 'react';

export default function CSRFField({ csrfHTML })
{
	return (
		<div dangerouslySetInnerHTML={ { __html: csrfHTML } } />
	);
}
