import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import FormModal from '../Form_Components/FormModal';
import TextInput from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';

const schema = Yup.object().shape({
	infobox_name: Yup.string()
		.required('Required')
		.matches(/^([A-Za-z][A-Za-z -]*)?[A-Za-z]$/, 'Alphabetic characters and interior hyphens and spaces only'),
});

export default function InfoboxCreationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

	return (
		<Formik
			initialValues={ { infobox_name: '' } }
			validationSchema={ schema }
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					await makeApiPostRequest(
						'/a/infobox',
						'create',
						{
							infobox_name: values.infobox_name,
						},
						globals.csrfTokens,
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
					title='Create Infobox Structure'
					submitButtonText='Submit'
					requiredFields={ [ 'infobox_name' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='infobox_name'
						labelText='Infobox Name'
						width={ 250 }
						autoComplete='off'
						hasError={ touched.infobox_name && errors.infobox_name }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
}
