import React, { ReactElement } from 'react';

import { Form } from 'formik';

import { Card }         from './Card';
import { SubmitButton } from './SubmitButton';
import { ErrorLabel }   from './ErrorLabel';

interface Props
{
	title: string;
	submitButtonText: string;
	requiredFields: string[];
	values: {[key: string]: string | boolean};
	errors: {[key: string]: any};
	submissionError: string | null;
	autoComplete?: 'on' | 'off';
	children: React.ReactNode;
};

export const FormModal = ({ title, submitButtonText, requiredFields, values, errors, submissionError, children, autoComplete='on' }: Props): ReactElement =>
{
	let submitButtonDisabled = Object.keys( errors ).length > 0
		|| requiredFields.some( field =>
			{
				const value = values[field];
				return ( typeof value === 'undefined'
					|| ( typeof value === 'string' && value.length === 0 ) );
			})

	return (
		<Card title={ title }>
			<Form className='form' autoComplete={ autoComplete }>
				{ children }

				<SubmitButton disabled={ submitButtonDisabled }>
					{ submitButtonText }
				</SubmitButton>

				{ submissionError && (
					<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>
				) }
			</Form>
		</Card>
	);
};
