import React, { useContext, useState } from 'react';

import { Formik, Form, Field, ErrorMessage } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import CSRFField from '../../Form Components/CSRFField';

const schema = Yup.object().shape({
	title: Yup.string()
		.required('Required'),
	wikitext: Yup.string()
});

export default function WikiPageEditForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [wikitext, setWikitext] = useState(null);

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
				}).then( () => {
					setSubmitting(false);
					closeModal();

					location.reload();
				});
			} }
		>
			{ ({ isSubmitting }) => (
				<Form>
					<label className="form-label" htmlFor="title"> Title </label>
					<Field name="title" />
					<ErrorMessage name="title" component="div" />

					<label className="form-label" htmlFor="wikitext"> Wikitext </label>
					<Field name="wikitext" component="textarea" />
					<ErrorMessage name="wikitext" />

					<button type="submit" disabled={ isSubmitting }> Submit </button>

					<CSRFField />
				</Form>
			) }
		</Formik>
	);
}
