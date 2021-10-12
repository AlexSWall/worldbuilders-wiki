import React from 'react';

import { Form } from 'formik';

import Card         from './Card';
import SubmitButton from './SubmitButton';
import ErrorLabel   from './ErrorLabel';

export default function FormModal({ title, submitButtonText, requiredFields, values, errors, submissionError, children, autoComplete='on' })
{
	let submitButtonDisabled = Object.keys(errors).length > 0
		|| requiredFields.some( field => values[field].length == 0 );

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
}
