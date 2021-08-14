import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

const schema = Yup.object().shape({
	email: Yup.string()
		.required('Required')
		.email()
});

export default function AccountRecoveryForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

	return (
		<Formik
			initialValues={ {
				email: '',
			} }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting, setErrors }) => {

				setSubmissionError(null);

				console.log('Posting...')

				fetch('/auth/', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'request password reset email',
						data: {
							email: values.email
						},
					}, globals.csrfTokens))
				}).then(async res => {
					if (res.ok)
					{
						setSubmitting(false);
						closeModal();
					}
					else
					{
						console.log('Error: Received status code ' + res.status + ' in response to POST request');

						const contentType = res.headers.get("content-type");

						if (contentType && contentType.indexOf("application/json") !== -1) {
							res.json().then(data => {
								if (data.error === 'Validation failure')
								{
									setErrors(data.validation_errors);
								}
								else
								{
									setSubmissionError(data.error);
									console.log('Error: ' + data.error);
								}
							});
						} else {
							res.text().then(text => {
								setSubmissionError(text);
								console.log('Error (text): ' + text);
							});
						}
					}
				}).catch( error => {
					console.log('Failed to make POST request...')
					console.log(error);
				});
			} }
		>
			{ ({ touched, setFieldTouched, handleChange, errors }) => (
				<div className='card'>
					<div className='card-header'>
						Account Recovery
					</div>
					<div className='card-body'>
						<Form className='form'>
							<TextInput
								formId='email'
								labelText='Email'
								width={ 250 }
								hasError={ touched.email && errors.email }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<SubmitButton disabled={ ! (Object.keys(errors).length == 0 && 'email' in touched ) }> Send Account Recovery Email </SubmitButton>

							{ submissionError
								? (<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>)
								: null }
						</Form>
					</div>
				</div>
			) }
		</Formik>
	);
}
