import React from 'react';

export default function SubmitButton({ text })
{
	return (
		<button type='submit' className='form-submit'>{ text }</button>
	);
}
