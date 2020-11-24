import React from 'react';

export default function AuthFormLink({ target, text })
{
	return (
		<div className='form-group'>
			<a href={ target }>{ text }</a>
		</div>
	);
}
