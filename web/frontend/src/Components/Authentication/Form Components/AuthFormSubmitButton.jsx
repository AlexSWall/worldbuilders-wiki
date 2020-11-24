import React from 'react';

export default function AuthFormSubmitButton({ text })
{
	return (
		<button type='submit' className='btn btn-default'>{ text }</button>
	);
}
