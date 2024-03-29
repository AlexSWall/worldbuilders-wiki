import React, { ReactElement, useContext, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal } from '../Form_Components/FormModal';
import { TextInput } from '../Form_Components/TextInput';

import { makeApiPostRequest } from 'utils/api';
import { getWikiPagePathAndHeading } from 'utils/wiki';

const schema = Yup.object().shape({
	page_path: Yup.string()
		.required('Required')
		.matches(/^([a-z][a-z-]*)?[a-z]$/, 'Lowercase characters and interior hyphens only'),
	title: Yup.string()
		.required('Required')
});

interface Props
{
	closeModal: () => void;
};

export const WikiPageCreationForm = ({ closeModal }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

	const [ wikiPagePath, _heading ] = getWikiPagePathAndHeading( window.location.hash );

	return (
		<Formik
			initialValues={ { page_path: wikiPagePath, title: '' } }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting, setErrors }) => {
				makeApiPostRequest(
					'/a/wiki',
					'create',
					{
						page_path: values.page_path,
						title: values.title
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
			} }
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange }) => (
				<FormModal
					title='Create Wiki Page'
					submitButtonText='Submit'
					requiredFields={ [ 'page_path', 'title' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='page_path'
						labelText='WikiPage Path/ID'
						width={ 250 }
						autoComplete='off'
						hasError={ !!(touched.page_path && errors.page_path) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
						initialValue={ wikiPagePath }
					/>

					<TextInput
						formId='title'
						labelText='WikiPage Title'
						width={ 250 }
						autoComplete='off'
						hasError={ !!(touched.title && errors.title) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
