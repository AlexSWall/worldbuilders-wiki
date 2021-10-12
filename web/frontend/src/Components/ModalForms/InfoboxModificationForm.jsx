import React, { useContext, useState } from 'react';

import { Formik, Form } from 'formik';
import * as Yup from 'yup';

import GlobalsContext from 'GlobalsContext';

import FormModal      from '../Form_Components/FormModal';
import SelectDropdown from '../Form_Components/SelectDropdown';
import WikiTextArea   from '../Form_Components/WikiTextArea';

import { makeApiPostRequest } from 'utils/api';

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

		fetch('/a/wiki?wikipage=' + wikiPagePath, {
			headers: {
				'Accept': 'application/json',
			}
		}).then(res => res.json())
		  .then(res => {
				const wikitext = res.wikitext;
				const regex = /^{{ Infobox +([A-Za-z][A-Za-z -]*)?[A-Za-z]$/;
				const matches = wikitext.match(regex);

				if ( matches != null && matches.length > 0 )
				{
					const match = matches[0];
					const infoboxName = match.substring(8).trim();

					fetch('/a/infobox?' + new URLSearchParams({
							infobox: infoboxName
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
				}
		});
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
			onSubmit={ async (values, { setSubmitting, setErrors }) =>
				{
					await makeApiPostRequest(
						'/a/infobox',
						'modify',
						{
							infobox_name: values.selected_infobox_name,
							structure: values.infobox_structure
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
				}
			}
		>
			{ ({ values, touched, errors, setFieldTouched, handleChange, initialValues, setFieldValue }) => (
				<FormModal
					title='Edit Infobox Structure'
					submitButtonText='Submit'
					requiredFields={ [ 'selected_infobox_name' ] }
					values={ values }
					errors={ errors }
					submissionError={ submissionError }
				>
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
				</FormModal>
			) }
		</Formik>
	);
}
