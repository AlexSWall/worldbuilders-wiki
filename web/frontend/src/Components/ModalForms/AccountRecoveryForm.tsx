import React, { ReactElement, useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal } from '../Form_Components/FormModal';
import { TextInput } from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';

const schema = Yup.object().shape({
	email: Yup.string()
		.required('Required')
		.email()
});

interface Props
{
	closeModal: () => void;
};

export const AccountRecoveryForm = ({ closeModal }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [submissionError, setSubmissionError] = useState<string | null>( null );

	return (
		<Formik
			initialValues={ {
				email: '',
			} }
			validationSchema={ schema }
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					await makeApiPostRequest(
						'/auth/',
						'request password reset email',
						{
							email: values.email
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
					title='Account Recovery'
					submitButtonText='Send Account Recovery Email'
					requiredFields={ [ 'email' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='email'
						labelText='Email'
						width={ 250 }
						hasError={ !!( touched.email && errors.email ) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
