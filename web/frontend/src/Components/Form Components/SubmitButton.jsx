import React from 'react';

export default function SubmitButton(props)
{
	return (
		<button type='submit' className='form-submit'>{ props.text }</button>
	);
}
