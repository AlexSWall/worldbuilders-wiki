import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import FormModal    from '../Form_Components/FormModal';
import TextInput    from '../Form_Components/TextInput';
import WikiTextArea from '../Form_Components/WikiTextArea';

import { makeApiPostRequest } from 'utils/api';

const schema = Yup.object().shape({
	title: Yup.string()
		.required('Required'),
	wikitext: Yup.string()
});

export default function WikiPageModificationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [wikitext, setWikitext] = useState(null);
	const [submissionError, setSubmissionError] = useState(null);

	const title = document.title;

	const hash = window.location.hash.substring(1);
	const [wikiPagePath,] = hash.split('#');

	fetch('/a/wiki?wikipage=' + wikiPagePath, {
		headers: {
			'Accept': 'application/json',
		}
	}) .then(res => res.json())
		.then(res => setWikitext(res.wikitext));

	return (wikitext === null)
		? ( <i> Fetching and loading content...  </i> )
		: ( <Formik
			initialValues={ { title: title, wikitext: wikitext } }
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
					globals.csrfTokens,
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
						hasError={ touched.title && errors.title }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
						initialValue={ initialValues.title }
					/>

					<WikiTextArea
						formId='wikitext'
						labelText='Wikitext'
						size={ { width: 250, height: 150 } }
						hasError={ touched.wikitext && errors.wikitext }
						setFieldTouched={ setFieldTouched }
						handleChange={ handleChange }
						initialValue={ initialValues.wikitext }
					/>
				</FormModal>
			) }
		</Formik>
	);
}
