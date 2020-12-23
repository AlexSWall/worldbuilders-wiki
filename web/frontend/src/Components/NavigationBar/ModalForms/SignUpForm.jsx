import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../../Form Components/TextInput';
import SubmitButton from '../../Form Components/SubmitButton';
import ErrorLabel from '../../Form Components/ErrorLabel';

const schema = Yup.object().shape({
	preferred_name: Yup.string()
		.matches(/[a-zA-Z ]*/, 'Must only contain letters and spaces')
		.max(30, 'Cannot be over 20 characters long'),
	username: Yup.string()
		.matches(/[a-zA-Z0-9]*/, 'Must only contain letters and numbers')
		.min(1, 'Required')
		.min(4, 'Must be at least 4 characters long')
		.max(20, 'Cannot be over 20 characters long'),
	email: Yup.string()
		.min(1, 'Required')
		.email(),
	password: Yup.string()
		.min(1, 'Required')
		.min(6, 'Must be at least 6 characters long')
		.max(30, 'Cannot be over 30 characters long'),
	password_confirm: Yup.string()
		.min(1, 'Required')
		.min(6, 'Must be at least 6 characters long')
		.oneOf([Yup.ref('password'), null], "Passwords do not match")
});

export default function SignUpForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

	return (
		<Formik
			initialValues={ {
				preferred_name: '',
				username: '',
				email: '',
				password: '',
				password_confirm: ''
			} }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting, setErrors }) => {
				console.log('Posting...')
				fetch('/auth/', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'sign up',
						data: {
							username: values.username,
							email: values.email,
							password: values.password,
							preferred_name: values.preferred_name
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
						Sign In
					</div>
					<div className='card-body'>
						<Form className='form'>
							<TextInput
								formId='preferred_name'
								labelText='Preferred Name'
								width={ 250 }
								hasError={ touched.preferred_name && errors.preferred_name }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='username'
								labelText='Username'
								width={ 250 }
								hasError={ touched.username && errors.username }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='email'
								labelText='Email'
								width={ 250 }
								hasError={ touched.email && errors.email }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='password'
								labelText='Password'
								type='password'
								width={ 250 }
								hasError={ touched.password && errors.password }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='password_confirm'
								labelText='Confirm Password'
								type='password'
								width={ 250 }
								hasError={ touched.password_confirm && errors.password_confirm }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<SubmitButton disabled={ !(Object.keys(errors).length == 0 && Object.keys(touched).length == 5) }> Sign Up </SubmitButton>

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
