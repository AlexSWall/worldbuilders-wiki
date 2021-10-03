import React, { useState } from 'react';

import { Field, ErrorMessage } from 'formik';

export default function WikiTextArea({ formId, labelText, size, hasError, setFieldTouched, handleChange, initialValue=undefined, value })
{
	const [ isEmpty, setIsEmpty ] = useState(initialValue === undefined || initialValue === '');

	if ( isEmpty && value )
	{
		setIsEmpty(false);
	}

	return (
		<div className='form-group'>
			<div style={ { display: 'inline-block' } }>
				<div className='form-input-wrapper'>
					<Field name={ formId }
						className={ (hasError ? 'form-input-has-error ' : '') + (! isEmpty ? 'has-content ' : '' ) + 'form-input' }
						style={ {
							// Using inline-block surrounded by block to avoid
							//	bizarre resizing width margin problems.
							display: 'block',
							width: 0.9 * window.innerWidth,
							height: 0.5 * window.innerHeight,
							resize: 'none',
							fontFamily: 'Roboto Mono, monospace',
							letterSpacing: '-.1rem',
							lineHeight: 1.5
						} }
						as='textarea'
						onChange={ e => {
							setIsEmpty(e.target.value === '');
							setFieldTouched(formId);
							handleChange(e);
						} }
					/>
					<label>{ labelText }</label>
					<span className="focus-border">
						<i></i>
					</span>
				</div>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: size.width } } />
		</div>
	);
}
