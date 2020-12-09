import React from 'react';

import { Field, ErrorMessage } from 'formik';

export default function TextArea({ formId, labelText, size, hasError, setFieldTouched, handleChange, })
{
	return (
		<div className='form-group'>
			<label className='form-label' style={ { width: size.width } } htmlFor={ formId }> { labelText } </label>
			<div style={ { display: 'block' } }>
			<Field name={ formId }
				className={ (hasError ? 'form-control-has-error ' : '') + 'form-control' }
				style={ {
					// Using inline-block surrounded by block to avoid
					//	bizarre resizing width margin problems.
					display: 'inline-block',
					width: 0.9 * window.innerWidth,
					height: 0.5 * window.innerHeight,
					resize: 'none'
				} }
				as='textarea'
				onChange={ e => {
					setFieldTouched(formId);
					handleChange(e);
				} }
			/>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: size.width } } />
		</div>
	);
}
