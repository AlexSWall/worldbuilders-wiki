import React from 'react';

import { Field, ErrorMessage } from 'formik';

export default function TextInput({ formId, labelText, width, autoComplete, hasError, setFieldTouched, handleChange })
{
	return (
		<div className='form-group'>
			<label className='form-label' style={ { width: width } } htmlFor={ formId }> { labelText } </label>
			<Field name={ formId }
				className={ (hasError ? 'form-control-has-error ' : '') + 'form-control' }
				autoComplete={ autoComplete || 'on' }
				style={ { width: width } }
				onChange={ e => {
					if (setFieldTouched)
						setFieldTouched(formId);
					handleChange(e);
				} }
			/>
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: width } } />
		</div>
	);
}
