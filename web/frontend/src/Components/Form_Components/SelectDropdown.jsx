import React from 'react';

import { Field } from 'formik';

import SelectOption from './SelectOption';

export default function SelectDropdown({ formId, labelText, setFieldTouched, handleChange, children, defaultText = '' })
{
	return (
		<div className='form-select'>
			<label htmlFor={ formId }>{ labelText }</label>
			<Field
				name={ formId }
				id={ formId }
				as={ select }
				className={ 'form-select' }
				onChange={ e => {
					setFieldTouched(formId);
					handleChange(e);
				} }
			>
				<SelectOption isDisabled={ true } value='' text={ defaultText }/>
				{ children }
			</Field>
		</div>
	);
}
