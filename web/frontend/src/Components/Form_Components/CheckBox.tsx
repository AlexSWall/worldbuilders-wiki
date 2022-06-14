import React, { ReactElement } from 'react';

import { Field } from 'formik';

interface Props
{
	formId: string;
	labelText: string;
};

export const CheckBox = ({ formId, labelText }: Props): ReactElement =>
{
	return (
		<div className='form-group'>
			<Field type="checkbox" name={ formId } id={ formId } />
			<label className='form-label' style={ { display: 'inline-block', marginLeft: 5 } } htmlFor={ formId }>
				{ labelText }
			</label>
		</div>
	);
};
