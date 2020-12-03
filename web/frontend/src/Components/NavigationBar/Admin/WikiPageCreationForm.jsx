import React, { useContext } from 'react';

import { Formik, Form, Field, ErrorMessage } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import CSRFField from '../../Form Components/CSRFField';

const schema = Yup.object().shape({
	page_path: Yup.string()
		.required('Required')
		.matches(/[a-z][a-z-]*[a-z]/, 'Must be only lowercase, optionally with hyphens within'),
	title: Yup.string()
		.required('Required')
});

export default function WikiPageCreationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	return (
		<Formik
			initialValues={ { page_path: '', title: '' } }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting }) => {
				fetch('/a/wiki', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'create',
						page_path: values.page_path,
						data: { title: values.title },
					}, globals.csrfTokens))
				}).then( () => {
					setSubmitting(false);
					closeModal();

					window.location.hash = '#' + values.page_path;
					location.reload();
				});
			} }
		>
			{ ({ isSubmitting }) => (
				//<form action='/a/Wiki_Page' method='post' autoComplete='off'>
				<Form>
					<Field name="page_path" />
					<ErrorMessage name="page_path" component="div" />
					<Field name="title" />
					<ErrorMessage name="title" component="div" />
					<button type="submit" disabled={ isSubmitting }> Submit </button>
					<CSRFField />
				</Form>
			) }
		</Formik>
	);
}
