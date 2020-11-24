import React, { useState } from 'react';

export default function AuthFormTextEntry({ formId, type, labelText, placeholder, oldValue, errors })
{
	const [value, setValue] = useState(oldValue || '');

	return (
		<div className={ errors ? 'form-group is-invalid' : 'form-group' }>
			<label className='form-label' htmlFor={ formId }>{ labelText }</label>
			<input
				className={ errors ? 'form-control has-error' : 'form-control' }
				type={ type }
				name={ formId }
				id={ formId }
				value={ value }
				placeholder={ placeholder }
				onChange={ e => setValue(e.target.value) }
			/>
			{ errors && <span className='help-block'>{ errors }</span> }
		</div>
	);
}
