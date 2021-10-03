import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import TextInput from '../Form_Components/TextInput';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

export default function InfoboxDeletionForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

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
			onSubmit={ (values, { setSubmitting }) => {
				fetch('/a/wiki', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'delete',
						data: {
							infobox_name: values.infobox_name,
						}
					}, globals.csrfTokens))
				}).then(async res => {
					if (res.ok)
					{
						setSubmitting(false);
						closeModal();

						window.location.hash = '#';
					}
					else
					{
						console.log('Error: Received status code ' + res.status + ' in response to POST request');

						const contentType = res.headers.get("content-type");
						console.log(res);

						if (contentType && contentType.indexOf("application/json") !== -1) {
							res.json().then(data => {
								console.log(data);
								console.log('Error: ' + data.error);
								setSubmissionError(data.error);
							});
						} else {
							res.text().then(text => {
								console.log('Error (text): ' + text);
								setSubmissionError(text);
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
						Delete Wiki Page
					</div>
					<div className='card-body'>
						<Form className='form'>
							<TextInput
								formId='page_path'
								labelText={'Enter page hash to confirm'}
								width={ 250 }
								autoComplete='off'
								hasError={ touched.page_path && errors.page_path }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
							/>

							<SubmitButton disabled={ Object.keys(errors).length != Object.keys(touched).length - 1 } />

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
