import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { TextInput } from '../Form_Components/TextInput';
import { SubmitButton } from '../Form_Components/SubmitButton';
import { ErrorLabel } from '../Form_Components/ErrorLabel';
import { WeakPasswordWarning } from '../Form_Components/WeakPasswordWarning';

import { computePasswordHash } from 'utils/crypto';
import { makeApiPostRequest } from 'utils/api';

const schema = Yup.object().shape({
	password_new: Yup.string()
		.min(1, 'Required')
		.min(8, 'Must be at least 8 characters long'),
	password_new_confirm: Yup.string()
		.min(1, 'Required')
		.oneOf([Yup.ref('password_new'), null], "Passwords do not match")
});

export const ResetPasswordForm = (): JSX.Element =>
{
	const globalState = useContext( GlobalStateContext );

	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

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

				setSubmissionError(null);

				const newPasswordFrontendHash = await computePasswordHash(values.password_new);

				console.log('Posting...')

				await makeApiPostRequest(
					'/auth/',
					'reset password',
					{
						email: email,
						identifier: identifier,
						password_new: newPasswordFrontendHash
					},
					globalState.csrfTokens,
					() => {
						window.location.href = '/';
					},
					setErrors,
					setSubmissionError,
					setSubmitting
				);
			} }
		>
			{ ({ values, touched, setFieldTouched, handleChange, errors }) => (
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
								autoComplete='new-password'
								width={ 250 }
								hasError={ !!(touched.password_new && errors.password_new) }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							>
								<WeakPasswordWarning password={ values.password_new } width={ 250 }/>
							</TextInput>

							<TextInput
								formId='password_new_confirm'
								labelText='Confirm New Password'
								type='password'
								autoComplete='new-password'
								width={ 250 }
								hasError={ !!(touched.password_new_confirm && errors.password_new_confirm) }
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
};
