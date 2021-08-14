import React, { useState } from 'react';

import { Field, ErrorMessage } from 'formik';

export default function TextInput({ formId, labelText, type, width, autoComplete, hasError, setFieldTouched, handleChange, children, initialValue=undefined })
{
	const [ isEmpty, setIsEmpty ] = useState(initialValue === undefined || initialValue === '');

	return (
		<div className='form-group'>
			<div className='form-input-wrapper'>
				<Field
					name={ formId }
					id={ formId }
					className={ (hasError ? 'form-input-has-error ' : '') + (! isEmpty ? 'has-content ' : '' ) + 'form-input' }
					type={ type || 'text' }
					autoComplete={ autoComplete || 'on' }
					style={ { width: width } }
					onChange={ e => {
						setIsEmpty(e.target.value === '');
						setFieldTouched(formId);
						handleChange(e);
					} }
				/>
				<label htmlFor={ formId }>{ labelText }</label>
				<span className="focus-border">
					<i></i>
				</span>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: width } } />
			{ children }
		</div>
	);
}
