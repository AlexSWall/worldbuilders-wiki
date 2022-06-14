import React, { ReactElement, useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal }           from '../Form_Components/FormModal';
import { TextInput }           from '../Form_Components/TextInput';
import { WeakPasswordWarning } from '../Form_Components/WeakPasswordWarning';

import { makeApiPostRequest }  from 'utils/api';
import { computePasswordHash } from 'utils/crypto';

const schema = Yup.object().shape({
	password_old: Yup.string()
		.required('Required')
		.min(8, 'Must be at least 8 characters long'),
	password_new: Yup.string()
		.required('Required')
		.min(8, 'Must be at least 8 characters long'),
	password_new_confirm: Yup.string()
		.required('Required')
		.oneOf([Yup.ref('password_new'), null], "Passwords do not match")
});

interface Props
{
	closeModal: () => void;
};

export const ChangePasswordForm = ({ closeModal }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [submissionError, setSubmissionError] = useState<string | null>( null );

	return (
		<Formik
			initialValues={ {
				password_old: '',
				password_new: '',
				password_new_confirm: ''
			} }
			validationSchema={ schema }
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					const oldPasswordFrontendHash = await computePasswordHash(values.password_old);
					const newPasswordFrontendHash = await computePasswordHash(values.password_new);

					await makeApiPostRequest(
						'/auth/',
						'change password',
						{
							password_old: oldPasswordFrontendHash,
							password_new: newPasswordFrontendHash
						},
						globalState.csrfTokens,
						() => {
							closeModal();
						},
						setErrors,
						setSubmissionError,
						setSubmitting
					);
				}
			}
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange }) => (
				<FormModal
					title='Change Password'
					submitButtonText='Change Password'
					requiredFields={ [ 'password_old', 'password_new', 'password_new_confirm' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='password_old'
						labelText='Old Password'
						type='password'
						width={ 250 }
						hasError={ !!( touched.password_old && errors.password_old ) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>

					<TextInput
						formId='password_new'
						labelText='New Password'
						type='password'
						autoComplete='new-password'
						width={ 250 }
						hasError={ !!( touched.password_new && errors.password_new ) }
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
						hasError={ !!( touched.password_new_confirm && errors.password_new_confirm ) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
