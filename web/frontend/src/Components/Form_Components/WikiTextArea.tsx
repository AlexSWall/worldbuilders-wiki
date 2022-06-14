import React, { ReactElement, useState } from 'react';

import classNames from 'classnames';

import { Field, ErrorMessage } from 'formik';

interface Props
{
	formId: string;
	labelText: string;
	hasError: boolean;
	setFieldTouched: ( field: string ) => void;
	handleChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
	initialValue?: string | undefined;
	value: string;
};

export const WikiTextArea = ({ formId, labelText, hasError, setFieldTouched, handleChange, initialValue=undefined, value }: Props): ReactElement =>
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
					<Field
						name={ formId }
						id={ formId }
						className={ classNames({
							'form-input': true,
							'has-content': !isEmpty,
							'form-input-has-error': hasError
						}) }
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
						onChange={ (e: React.ChangeEvent<HTMLInputElement>) => {
							setIsEmpty( e.target.value === '' );
							setFieldTouched( formId );
							handleChange( e );
						} }
					/>
					<label htmlFor={ formId }>{ labelText }</label>
					<span className="focus-border">
						<i></i>
					</span>
				</div>
			</div>
			<ErrorMessage name={ formId } component='span' className='form-error' />
		</div>
	);
};
