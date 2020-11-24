import React, { useState } from 'react';

export default function TextArea({ formId, labelText })
{
	const [value, setValue] = useState('');

	return (
		<div className='form-group'>
			<label className='form-label' htmlFor={ formId }>{ labelText }</label>
			<textarea
				className='form-control'
				type='text'
				name={ formId }
				id={ formId }
				value={ value }
				onChange={ (e) => setValue(e.target.value) }
			/>
		</div>
	);
}

