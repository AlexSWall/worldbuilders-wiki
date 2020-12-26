import React from 'react';

export default function SubmitButton({ disabled, children })
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
}
