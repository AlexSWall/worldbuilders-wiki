import React, { useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import FormModal           from '../Form_Components/FormModal';
import TextInput           from '../Form_Components/TextInput';
import WeakPasswordWarning from '../Form_Components/WeakPasswordWarning';

import { makeApiPostRequest }  from 'utils/api';
import { computePasswordHash } from 'utils/crypto';

const schema = Yup.object().shape({
	preferred_name: Yup.string()
		.matches(/[a-zA-Z ]*/, 'Must only contain letters and spaces')
		.max(30, 'Cannot be over 20 characters long'),
	username: Yup.string()
		.required('Required')
		.matches(/^[a-zA-Z0-9]+$/, 'Must only contain letters and numbers')
		.min(4, 'Must be at least 4 characters long')
		.max(20, 'Cannot be over 20 characters long'),
	email: Yup.string()
		.required('Required')
		.email('Email must be valid'),
	password: Yup.string()
		.min(8, 'Must be at least 8 characters long')
		.required('Required'),
	password_confirm: Yup.string()
		.required('Required')
		.oneOf([Yup.ref('password'), null], "Passwords do not match")
});

export default function SignUpForm({ closeModal })
{
	const globalState = useContext( GlobalStateContext );

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
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					const passwordFrontendHash = await computePasswordHash(values.password);

					await makeApiPostRequest(
						'/auth/',
						'sign up',
						{
							username: values.username,
							email: values.email,
							password: passwordFrontendHash,
							preferred_name: values.preferred_name
						},
						globalState.csrfTokens,
						() => {
							closeModal();

							window.location.hash = '#' + values.page_path;
						},
						setErrors,
						setSubmissionError,
						setSubmitting
					);
				}
			}
		>
			{ ({ values, touched, setFieldTouched, handleChange, errors }) => (
				<FormModal
					title='Sign Up'
					submitButtonText='Sign Up'
					requiredFields={ [ 'username', 'email', 'password', 'password_confirm' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
					autoComplete='off'
				>
					<TextInput
						formId='preferred_name'
						labelText='Preferred Name'
						type='search'
						autoComplete='off'
						width={ 250 }
						hasError={ touched.preferred_name && errors.preferred_name }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>

					<TextInput
						formId='username'
						labelText='Username'
						type='search'
						autoComplete='off'
						width={ 250 }
						hasError={ touched.username && errors.username }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>

					<TextInput
						formId='email'
						labelText='Email'
						type='search'
						autoComplete='off'
						width={ 250 }
						hasError={ touched.email && errors.email }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>

					<TextInput
						formId='password'
						labelText='Password'
						type='password'
						autoComplete='new-password'
						width={ 250 }
						hasError={ touched.password && errors.password }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					>
						<WeakPasswordWarning password={ values.password } width={ 250 }/>
					</TextInput>

					<TextInput
						formId='password_confirm'
						labelText='Confirm Password'
						type='password'
						autoComplete='new-password'
						width={ 250 }
						hasError={ touched.password_confirm && errors.password_confirm }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
}
