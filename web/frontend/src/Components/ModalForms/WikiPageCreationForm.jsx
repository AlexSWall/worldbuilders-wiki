import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

const schema = Yup.object().shape({
	page_path: Yup.string()
		.required('Required')
		.matches(/^([a-z][a-z-]*)?[a-z]$/, 'Lowercase characters and interior hyphens only'),
	title: Yup.string()
		.required('Required')
});

export default function WikiPageCreationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [submissionError, setSubmissionError] = useState(null);

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
				}).then(async res => {
					if (res.ok)
					{
						setSubmitting(false);
						closeModal();

						window.location.hash = '#' + values.page_path;
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
			{ ({ touched, setFieldTouched, handleChange, errors }) => (
				<div className='card'>
					<div className='card-header'>
						Create a Wiki Page
					</div>
					<div className='card-body'>
						<Form className='form'>
							<TextInput
								formId='page_path'
								labelText='WikiPage Path/ID'
								width={ 250 }
								autoComplete='off'
								hasError={ touched.page_path && errors.page_path }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<TextInput
								formId='title'
								labelText='WikiPage Title'
								width={ 250 }
								autoComplete='off'
								hasError={ touched.title && errors.title }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<SubmitButton disabled={ Object.keys(errors).length != Object.keys(touched).length - 2 } />

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
