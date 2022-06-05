import React, { useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import FormModal from '../Form_Components/FormModal';
import TextInput from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';

export default function InfoboxDeletionForm({ closeModal })
{
	const globalState = useContext( GlobalStateContext );

	const [submissionError, setSubmissionError] = useState(null);

	const pagePath = window.location.hash.substring(1).split('#')[0];

	const schema = Yup.object().shape({
		page_path: Yup.string()
		.required('Required')
		.matches(/^\/?#?([a-z][a-z-]*)?[a-z]?$/, 'Must be only lowercase, optionally with hyphens within')
		.matches(new RegExp('^/?#?' + pagePath + '$', "g"), 'Must match \'' + pagePath + '\'')
	});

	return (
		<Formik
			initialValues={ { page_path: '' } }
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
					title='Delete Wiki Page'
					submitButtonText='Change Password'
					requiredFields={ [ 'page_path' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='page_path'
						labelText={'Enter page hash to confirm'}
						width={ 250 }
						autoComplete='off'
						hasError={ touched.page_path && errors.page_path }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
}
