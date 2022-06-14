import React, { ReactElement, useContext, useEffect, useState } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import { GlobalStateContext } from 'GlobalState';

import { FormModal }    from '../Form_Components/FormModal';
import { Card }         from '../Form_Components/Card';
import { TextInput }    from '../Form_Components/TextInput';
import { WikiTextArea } from '../Form_Components/WikiTextArea';

import { ApiGetWikiText, makeApiGetRequest, makeApiPostRequest } from 'utils/api';
import { getWikiPagePathAndHeading } from 'utils/wiki';
import useStateInitiallyNull from 'utils/hooks/useStateInitiallyNull';

const schema = Yup.object().shape({
	title: Yup.string()
		.required('Required'),
	wikitext: Yup.string()
});

interface Props
{
	closeModal: () => void;
	setHasUnsavedState: ( hasUnsavedState: boolean ) => void;
};

export const WikiPageModificationForm = ({ closeModal, setHasUnsavedState }: Props): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	const [ wikitext, setWikitext ] = useStateInitiallyNull<string>();
	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

	const [ wikiPagePath, _heading ] = getWikiPagePathAndHeading( window.location.hash );

	useEffect( () => {
		makeApiGetRequest<ApiGetWikiText>(
			`/a/wiki?wikipage=${wikiPagePath}`,
			data => setWikitext( data.wikitext )
		);
	}, []);

	return ( wikitext === null )
		? ( <Card title='Fetching and loading content...'/> )
		: ( <Formik
			initialValues={ { title: document.title, wikitext: wikitext } }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting, setErrors }) => {
				makeApiPostRequest(
					'/a/wiki',
					'modify',
					{
						page_path: wikiPagePath,
						title: values.title,
						content: values.wikitext
					},
					globalState.csrfTokens,
					() => {
						closeModal();

						window.dispatchEvent(new HashChangeEvent("hashchange"));
					},
					setErrors,
					setSubmissionError,
					setSubmitting
				);
			} }
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange, initialValues }) => (
				<FormModal
					title='Edit Wiki Page'
					submitButtonText='Submit'
					requiredFields={ [ 'title' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
					<TextInput
						formId='title'
						labelText='WikiPage Title'
						width={ 250 }
						autoComplete='off'
						hasError={ !!(touched.title && errors.title) }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
						initialValue={ initialValues.title }
					/>

					<WikiTextArea
						formId='wikitext'
						labelText='Wikitext'
						hasError={ !!(touched.wikitext && errors.wikitext) }
						setFieldTouched={ setFieldTouched }
						handleChange={ e => {
							// We've changed the wikitext entry, so set that we have
							// unsaved state before passing on to default handleChange.
							setHasUnsavedState(true);
							return handleChange(e);
						} }
						initialValue={ initialValues.wikitext }
						value={ values.wikitext }
					/>
				</FormModal>
			) }
		</Formik>
	);
};
