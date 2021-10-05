import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import SelectDropdown from '../Form_Components/SelectDropdown';
import WikiTextArea from '../Form_Components/WikiTextArea';
import SubmitButton from '../Form_Components/SubmitButton';
import ErrorLabel from '../Form_Components/ErrorLabel';

const schema = Yup.object().shape({
	selected_infobox_name: Yup.string()
		.required('Required'),
	infobox_structure: Yup.string()
});

export default function InfoboxModificationForm({ closeModal })
{
	const globals = useContext(GlobalsContext);

	const [ infoboxNames, setInfoboxNames ] = useState( null );
	const [ infoboxValidation, setInfoboxValidation ] = useState( schema );
	const [ initialInfoboxStructure, setInitialInfoboxStructure ] = useState( '' );
	const [ submissionError, setSubmissionError ] = useState( null );

	{
		// Check infobox of page and set currently-selected infobox dropdown to
		// have the value of that infobox name...
		const hash = window.location.hash.substring(1);
		const [ wikiPagePath, ] = hash.split('#');
		// TODO: Finish
	}

	if ( infoboxNames == null )
	{
		fetch('/a/infobox', {
			headers: {
				'Accept': 'application/json',
			}
		}).then( res => res.json() )
		  .then( res => {
				const infoboxNames = res.infobox_names;

				setInfoboxNames( infoboxNames );

				setInfoboxValidation( Yup.object().shape({
					selected_infobox_name: Yup.string()
						.required('Required')
						.oneOf( infoboxNames, 'Must be an existing infobox' ),
					infobox_structure: Yup.string()
				}) );
			});
	}

	return (
		<Formik
			initialValues={ { selected_infobox_name: '', infobox_structure: initialInfoboxStructure } }
			validationSchema={ infoboxValidation }
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
						data: {
							infobox_name: values.selected_infobox_name,
							structure: values.infobox_structure
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
			{ ({ touched, setFieldTouched, setFieldValue, handleChange, handleBlur, initialValues, values, errors }) => {
				return <div className='card'>
					<div className='card-header'>
						Edit Infobox Structure
					</div>
					<div className='card-body'>
						<Form className='form'>
							<SelectDropdown
								formId='selected_infobox_name'
								labelText='Infobox Name'
								width={ 250 }
								hasError={ touched.selected_infobox_name && errors.selected_infobox_name }
								setFieldTouched={ setFieldTouched }
								setValue={ selectedInfoboxName => {
									setFieldValue( 'selected_infobox_name', selectedInfoboxName );

									fetch('/a/infobox?' + new URLSearchParams({
											infobox: selectedInfoboxName
										}),
										{
											headers: {
												'Accept': 'application/json',
											}
										}
									)	.then( res => res.json() )
										.then( res => {
											const structureText = res.infobox_structure_text;
											setInitialInfoboxStructure( structureText );
											setFieldValue( 'infobox_structure', structureText );
											setFieldTouched( 'infobox_structure' );
										}
									);
								} }
								options={ infoboxNames }
								defaultText={ 'Choose an infobox...' }
							/>

							<WikiTextArea
								formId='infobox_structure'
								labelText='Infobox Structure'
								size={ { width: 250, height: 150 } }
								hasError={ touched.infobox_structure && errors.infobox_structure }
								setFieldTouched={ setFieldTouched }
								handleChange={ handleChange }
								initialValue={ initialValues.infobox_structure }
								value={ values.infobox_structure }
							/>

							<SubmitButton disabled={ Object.keys(errors).length > 0 } />

							{ submissionError
								? (<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>)
								: null }
						</Form>
					</div>
				</div>;
			} }
		</Formik>
	);
}
