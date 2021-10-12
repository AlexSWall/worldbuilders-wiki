import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import FormModal from '../Form_Components/FormModal';
import TextInput from '../Form_Components/TextInput';
import CheckBox  from '../Form_Components/CheckBox';

import AccountRecoveryForm from './AccountRecoveryForm';

import { makeApiPostRequest }  from 'utils/api';
import { computePasswordHash } from 'utils/crypto';

const schema = Yup.object().shape({
	identity: Yup.string()
		.required('Required'),
	password: Yup.string()
		.required('Required')
		.min(8, 'Must be at least 8 characters long'),
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
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					const passwordFrontendHash = await computePasswordHash(values.password);

					await makeApiPostRequest(
						'/auth/',
						'sign in',
						{
							identity: values.identity,
							password: passwordFrontendHash,
							remember_me: values.rememberMe
						},
						globals.csrfTokens,
						() => {
							closeModal();

							location.reload();
						},
						setErrors,
						setSubmissionError,
						setSubmitting
					);
				}
			}
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange }) => {
				// Required by Firefox auto-fill; if 'password' is given a value
				// by auto-fill, the 'Required' error still exists in errors.
				// Hence, we need to delete it manually.
				if ( values.password )
					delete errors.password;

				return (
					<FormModal
						title='Sign In'
						submitButtonText='Sign In'
						requiredFields={ [ 'identity', 'password' ] }
						values={ values }
						errors={ errors }
						submissionError={ submissionError }
					>
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
									setModalComponent( () => AccountRecoveryForm );
								} }>
									Forgotten your password?
								</a>
							</label>
						</div>

						<div className='form-group'>
							<label className='form-label' style={ { width: 250 } } >
								{ 'Don\'t have an account? ' }
								<a href='#'>
									Sign up.
								</a>
							</label>
						</div>

						<CheckBox
							formId='rememberMe'
							labelText='Keep me signed in'
						/>
					</FormModal>
				)
			} }
		</Formik>
	);
}
