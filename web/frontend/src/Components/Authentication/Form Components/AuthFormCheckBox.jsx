import React from 'react';

export default function AuthFormCheckBox({ formId, text })
{
	return (
		<div className='form-group form-check'>
			<input type='checkbox' className="form-check-input" name={ formId } id={ formId } />
			<label className='form-label form-check-label' htmlFor={ formId }>{ text }</label>
		</div>
	);
}
