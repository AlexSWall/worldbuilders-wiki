import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

import { computePasswordHash } from 'utils/crypto'

const schema = Yup.object().shape({
	password_new: Yup.string()
		.min(1, 'Required')
		.min(6, 'Must be at least 6 characters long')
		.max(30, 'Cannot be over 30 characters long'),
	password_new_confirm: Yup.string()
		.min(1, 'Required')
		.min(6, 'Must be at least 6 characters long')
		.oneOf([Yup.ref('password_new'), null], "Passwords do not match")
});

export default function ResetPasswordForm()
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

	const urlParams = new URLSearchParams(window.location.search);
	const email = urlParams.get('email');
	const identifier = urlParams.get('identifier');

	return (
		<Formik
			initialValues={ {
				password_new: '',
				password_new_confirm: ''
			} }
			validationSchema={ schema }
			onSubmit={ async (values, { setSubmitting, setErrors }) => {

				const newPasswordFrontendHash = await computePasswordHash(values.password_new);

				console.log('Posting...')

				try
				{
					const res = await fetch('/auth/', {
						method: 'post',
						headers: {
							'Accept': 'application/json, text/plain, */*',
							'Content-Type': 'application/json'
						},
						body: JSON.stringify(Object.assign({}, {
							action: 'reset password',
							data: {
								email: email,
								identifier: identifier,
								password_new: newPasswordFrontendHash,
							},
						}, globals.csrfTokens))
					});

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
				}
				catch( error )
				{
					console.log('Failed to make POST request...')
					console.log(error);
				};
			} }
		>
			{ ({ touched, setFieldTouched, handleChange, errors }) => (
				<div className='card'>
					<div className='card-header'>
						Reset Password
					</div>
					<div className='card-body'>
						<Form className='form'>
							<TextInput
								formId='password_new'
								labelText='New Password'
								type='password'
								width={ 250 }
								hasError={ touched.password_new && errors.password_new }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='password_new_confirm'
								labelText='Confirm New Password'
								type='password'
								width={ 250 }
								hasError={ touched.password_new_confirm && errors.password_new_confirm }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<SubmitButton disabled={ !(Object.keys(errors).length == 0 && Object.keys(touched).length == 2) }> Reset Password </SubmitButton>

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
