import React from 'react';

import { Field, ErrorMessage } from 'formik';

export default function SelectDropdown({ formId, labelText, width, setFieldTouched, handleChange, handleBlur, value, options, defaultText = '' })
{
	return (
		<div className='form-group'>
			<label htmlFor={ formId }>{ labelText }</label>
			<Field
				name={ formId }
				id={ formId }
				list={ formId + '-list' }
				className={ 'form-select' }
				onChange={ e => {
					setFieldTouched(formId);
					handleChange(e);
				} }
			/>
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
			<ErrorMessage name={ formId } component='span' className='form-error' style={ { width: width } } />
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
