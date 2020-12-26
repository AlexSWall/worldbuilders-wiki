import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';
import CheckBox from '../Form_Components/CheckBox';

import AccountRecoveryForm from './AccountRecoveryForm';

const schema = Yup.object().shape({
	identity: Yup.string()
		.min(1, 'Required'),
	password: Yup.string()
		.min(1, 'Required'),
	rememberMe: Yup.boolean()
});

export default function SignInForm({ closeModal, setModalComponent })
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

	return (
		<Formik
			initialValues={ {
				identity: '',
				password: '',
				rememberMe: false
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
						action: 'sign in',
						data: {
							identity: values.identity,
							password: values.password,
							rememberMe: values.rememberMe
						},
					}, globals.csrfTokens))
				}).then(async res => {
					if (res.ok)
					{
						setSubmitting(false);
						closeModal();

						location.reload();
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
								formId='identity'
								labelText='Username or Email'
								width={ 250 }
								hasError={ touched.identity && errors.identity }
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

							<div className='form-group'>
								<label className='form-label' style={ { width: 250 } } >
									<a href='#' onClick={ () => {
										setModalComponent(() => AccountRecoveryForm);
									} }>Forgotten your password?</a>
								</label>
							</div>

							<div className='form-group'>
								<label className='form-label' style={ { width: 250 } } >
									{ 'Don\'t have an account? ' }
									<a href='#'>Sign up.</a>
								</label>
							</div>

							<CheckBox
								formId='rememberMe'
								labelText='Keep me signed in'
							/>

							<SubmitButton disabled={ !(Object.keys(errors).length == 0 && 'identity' in touched && 'password' in touched) }> Sign In </SubmitButton>

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
