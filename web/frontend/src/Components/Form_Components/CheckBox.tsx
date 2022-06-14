import React from 'react';

import { Field } from 'formik';

export default function CheckBox({ formId, labelText })
{
	return (
		<div className='form-group'>
			<Field type="checkbox" name={ formId } id={ formId } />
			<label className='form-label' style={ { display: 'inline-block', marginLeft: 5 } } htmlFor={ formId }> { labelText } </label>
		</div>
	);
}
