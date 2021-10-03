import React, { useState } from 'react';

import { Field, ErrorMessage } from 'formik';

export default function SelectDropdown({ formId, labelText, width, hasError, setFieldTouched, handleChange, handleBlur, value, options, defaultText = '' })
{
	const [ isEmpty, setIsEmpty ] = useState( false );

	return (
		<div className='form-group'>
			<div className='form-input-wrapper'>
				<Field
					name={ formId }
					id={ formId }
					list={ formId + '-list' }
					className={ (hasError ? 'form-input-has-error ' : '') + (! isEmpty ? 'has-content ' : '' ) + 'form-input' }
					onChange={ e => {
						console.log(e.target.value);
						setIsEmpty(e.target.value === '');
						setFieldTouched(formId);
						handleChange(e);
					} }
				/>
				<label htmlFor={ formId } style={ { pointerEvents: 'none' } }>{ labelText }</label>
				<span className="focus-border">
					<i></i>
				</span>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: width } } />
			<datalist id={ formId + '-list' } >
				{ options && options.map( ( option ) => {
					return (
						<option
							value={ option }
							key={ option }
						>
							{ option }
						</option>
					);
				})}
			</datalist>
		</div>
	);
}



			// <Select
			// 	name={ formId }
			// 	id={ formId }
			// 	className='form-select'
			// 	placeholder={ defaultText }
			// 	value={ value }
			// 	onBlur={ handleBlur }
			// 	onChange={ selectedOption => {
			// 		let event = { target : { name: formId, value: selectedOption } };
			// 		handleChange(event);
			// 	} }
			// 	onBlur={ () => {
			// 		handleBlur( { target: { name: formId } } );
			// 	} }
			// 	options={ options }
			// />
