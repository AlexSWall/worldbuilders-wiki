import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import SelectDropdown from '../Form_Components/SelectDropdown';
import WikiTextArea from '../Form_Components/WikiTextArea';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

const schema = Yup.object().shape({
	name: Yup.string()
		.required('Required'),
	infobox_structure: Yup.string()
});

export default function InfoboxModificationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [infobox_structure, setInfoboxStructure] = useState('');
	const [submissionError, setSubmissionError] = useState(null);

	const hash = window.location.hash.substring(1);
	const [infoboxPath,] = hash.split('#');

	fetch('/a/infobox?infobox=' + infoboxPath, {
		headers: {
			'Accept': 'application/json',
		}
	}) .then(res => res.json())
		.then(res => setInfoboxStructure(res.infobox_structure));

	return (infobox_structure === null)
		? ( <i> Fetching and loading content...  </i> )
		: ( <Formik
			initialValues={ { title: title, infobox_structure: infobox_structure } }
			validationSchema={ schema }
			onSubmit={ (values, { setSubmitting }) => {
				console.log('Submitting...')
				fetch('/a/infobox', {
					method: 'post',
					headers: {
						'Accept': 'application/json, text/plain, */*',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(Object.assign({}, {
						action: 'modify',
						page_path: infoboxPath,
						data: {
							title: values.title,
							content: values.infobox_structure
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
						Edit Infobox Structure
					</div>
					<div className='card-body'>
						<Form className='form'>
							<SelectDropdown
								formId='infobox'
								labelText='Infobox Title'
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
								defaultText={ 'Choose an infobox...' }
							>
								<SelectOption value='foo' text='asdasd'/>
							</SelectDropdown>

							<WikiTextArea
								formId='infobox_structure'
								labelText='Infobox Structure'
								size={ { width: 250, height: 150 } }
								hasError={ touched.infobox_structure && errors.infobox_structure }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
								initialValue={ initialValues.infobox_structure }
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
