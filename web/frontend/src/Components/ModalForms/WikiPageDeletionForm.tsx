import React, { ReactElement, useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal } from '../Form_Components/FormModal';
import { TextInput } from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';
import { getWikiPagePathAndHeading } from 'utils/wiki';

interface Props
{
	closeModal: () => void;
};

export const WikiPageDeletionForm = ({ closeModal }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

	const [ wikiPagePath, _heading ] = getWikiPagePathAndHeading( window.location.hash );

	const schema = Yup.object().shape({
		page_path: Yup.string()
		.required('Required')
		.matches(/^\/?#?([a-z][a-z-]*)?[a-z]?$/, 'Must be only lowercase, optionally with hyphens within')
		.matches(new RegExp('^/?#?' + wikiPagePath + '$', "g"), 'Must match \'' + wikiPagePath + '\'')
	});

	return (
		<Formik
			initialValues={ { page_path: '' } }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting, setErrors }) => {
				makeApiPostRequest(
					'/a/wiki',
					'delete',
					{
						page_path: values.page_path
					},
					globalState.csrfTokens,
					() => {
						closeModal();

						// Return to previous page.
						window.history.back();
					},
					setErrors,
					setSubmissionError,
					setSubmitting
				);
			} }
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange }) => (
				<FormModal
					title='Delete Wiki Page'
					submitButtonText='Submit'
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
						hasError={ !!(touched.page_path && errors.page_path) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
