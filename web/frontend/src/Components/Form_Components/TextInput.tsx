import React, { HTMLInputTypeAttribute, ReactElement, useState } from 'react';

import classNames from 'classnames';

import { Field, ErrorMessage } from 'formik';

interface Props
{
	formId: string;
	labelText: string;
	type?: HTMLInputTypeAttribute;
	width: number;
	autoComplete?: 'on' | 'off' | 'new-password';
	hasError: boolean;
	setFieldTouched: ( field: string ) => void;
	handleChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
	initialValue?: string | null;
	children?: React.ReactNode | null;
};

export const TextInput = ({ formId, labelText, type = 'text', width, autoComplete = 'on', hasError, setFieldTouched, handleChange, children, initialValue = null }: Props): ReactElement =>
{
	const [ isEmpty, setIsEmpty ] = useState<boolean>(initialValue === null || initialValue === '');

	return (
		<div className='form-group'>
			<div className='form-input-wrapper'>
				<Field
					name={ formId }
					id={ formId }
					className={ classNames({
						'form-input': true,
						'has-content': !isEmpty,
						'form-input-has-error': hasError
					}) }
					type={ type }
					autoComplete={ autoComplete }
					style={ { width: width } }
					onChange={ ( e: React.ChangeEvent<HTMLInputElement> ) => {
						setIsEmpty(e.target.value === '');
						setFieldTouched( formId );
						handleChange( e );
					} }
				/>
				<label htmlFor={ formId }>{ labelText }</label>
				<span className="focus-border">
					<i></i>
				</span>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' />
			{ children }
		</div>
	);
};
