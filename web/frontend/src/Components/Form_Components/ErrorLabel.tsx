import React from 'react';

export default function ErrorLabel({ width, children })
{
	return (
		<div className='form-group'>
			<label className='form-label-error' style={ { width: width } }>
				{ children }
			</label>
		</div>
	);
}
