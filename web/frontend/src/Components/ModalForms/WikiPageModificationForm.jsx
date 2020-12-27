import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import TextArea from '../Form_Components/TextArea';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

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
			onSubmit={ (values, { setSubmitting }) => {
				console.log('Submitting...')
				fetch('/a/wiki', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'modify',
						page_path: wikiPagePath,
						data: {
							title: values.title,
							content: values.wikitext
						},
					}, globals.csrfTokens))
				}).then(async res => {
					if (res.ok)
					{
						setSubmitting(false);
						closeModal();

						window.dispatchEvent(new HashChangeEvent("hashchange"));
					}
					else
					{
						console.log('Error: Received status code ' + res.status + ' in response to POST request');

						const contentType = res.headers.get("content-type");

						if (contentType && contentType.indexOf("application/json") !== -1) {
							res.json().then(data => {
								setSubmissionError(data.error);
								console.log('Error: ' + data.error);
							});
						} else {
							res.text().then(text => {
								setSubmissionError(text);
								console.log('Error (text): ' + text);
							});
						}
					}
				}).catch( error => {
					console.log('Failed to make POST request...')
					console.log(error);
				});
			} }
		>
			{ ({ touched, setFieldTouched, handleChange, initialValues, errors }) => (
				<div className='card'>
					<div className='card-header'>
						Edit Wiki Page
					</div>
					<div className='card-body'>
						<Form className='form'>
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

							<TextArea
								formId='wikitext'
								labelText='Wikitext'
								size={ { width: 250, height: 150 } }
								hasError={ touched.wikitext && errors.wikitext }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
								initialValue={ initialValues.wikitext }
							/>

							<SubmitButton disabled={ Object.keys(errors).length > 0 } />
							
							{ submissionError
								? (<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>)
								: null }
						</Form>
					</div>
				</div>
			) }
		</Formik>
	);
}
