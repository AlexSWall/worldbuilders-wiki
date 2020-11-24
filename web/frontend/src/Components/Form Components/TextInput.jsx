import React, {setState} from 'react';

export default function TextInput({ formId, labelText })
{
	const [value, setValue] = setState('');

	return (
		<div className='form-group'>
			<label className='form-label' htmlFor={ formId }>{ labelText }</label>
			<input
				className='form-control'
				type='text'
				name={ formId }
				id={ formId }
				value={ value }
				onChange={ (e) => { if ( e.target.value.length <= 30 ) setValue(e.target.value); } }
			/>
		</div>
	);
}
