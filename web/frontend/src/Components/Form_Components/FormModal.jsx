import React from 'react';

import { Formik, Form } from 'formik';

import SubmitButton from './SubmitButton';
import ErrorLabel   from './ErrorLabel';

export default function FormModal({ title, submitButtonText, requiredFields, values, errors, submissionError, children, autoComplete='on' })
{
	let submitButtonDisabled = Object.keys(errors).length > 0
		|| requiredFields.some( field => values[field].length == 0 );

	return (
		<div className='card'>
			<div className='card-header'>
				{ title }
			</div>
			<div className='card-body'>
				<Form className='form' autoComplete={ autoComplete }>
					{ children }

					<SubmitButton disabled={ submitButtonDisabled }>
						{ submitButtonText }
					</SubmitButton>

					{ submissionError && (
						<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>
					) }
				</Form>
			</div>
		</div>
	);
}
