import React, { ReactElement, useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal } from '../Form_Components/FormModal';
import { TextInput } from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';

interface Props
{
	closeModal: () => void;
};

export const InfoboxDeletionForm = ({ closeModal }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

	const schema = Yup.object().shape({
		infobox_name: Yup.string()
		.required('Required')
		.matches(/^([A-Za-z][A-Za-z -]*)?[A-Za-z]$/, 'Alphabetic characters and interior hyphens and spaces only'),
	});

	return (
		<Formik
			initialValues={ { infobox_name: '' } }
			validationSchema={ schema }
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					await makeApiPostRequest(
						'/a/infobox',
						'delete',
						{
							infobox_name: values.infobox_name,
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
					title='Delete Infobox'
					submitButtonText='Change Password'
					requiredFields={ [ 'infobox_name' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='infobox_name'
						labelText={'Enter the infobox name to delete'}
						width={ 250 }
						autoComplete='off'
						hasError={ !!(touched.infobox_name && errors.infobox_name) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
