import React, { useContext, useEffect, useState } from 'react';

import { Formik } from 'formik';
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

export default function InfoboxModificationForm({ closeModal, setHasUnsavedState })
{
	const globals = useContext(GlobalsContext);

	const [ [ initialInfoboxName, initialInfoboxStructure ], setInitialInfoboxData ] = useState( ['', ''] );
	const [ infoboxNames, setInfoboxNames ] = useState( null );
	const [ infoboxValidation, setInfoboxValidation ] = useState( schema );
	const [ submissionError, setSubmissionError ] = useState( null );

	// On the initial load, obtain the infobox names to populate the select
	// dropdown.
	useEffect( () =>
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
	}, []);

	// On the initial load, check whether our current wikipage has an infobox,
	// and, if it does, use it as the initial infobox structure to edit.
	useEffect( () =>
	{
		// We're on the initial load; let's set the initial infobox name to be the
		// infobox on the current page if it has one...
		const hash = window.location.hash.substring(1);
		const [ wikiPagePath, ] = hash.split('#');

		fetch('/a/wiki?wikipage=' + wikiPagePath, {
			headers: {
				'Accept': 'application/json',
			}
		}).then(res => res.json())
		  .then(res => {
				const wikitext = res.wikitext;
				const regex = /^{{ +([A-Za-z][A-Za-z-]*)?[A-Za-z]/;
				const matches = wikitext.match(regex);

				if ( matches != null && matches.length > 0 )
				{
					const match = matches[0];
					const infoboxName = match.substring(3).trim();

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
							setInitialInfoboxData([ infoboxName, structureText ]);
						}
					);
				}
		});
	}, []);

	return (
		<Formik
			initialValues={ { selected_infobox_name: initialInfoboxName, infobox_structure: initialInfoboxStructure } }
			enableReinitialize={ true }
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
									setInitialInfoboxData([ selectedInfoboxName, structureText ]);
									setFieldValue( 'infobox_structure', structureText );
									setFieldTouched( 'infobox_structure' );
									setHasUnsavedState(false);
								}
							);
						} }
						options={ infoboxNames }
						initialValue={ initialValues.selected_infobox_name }
					/>

					<WikiTextArea
						formId='infobox_structure'
						labelText='Infobox Structure'
						size={ { width: 250, height: 150 } }
						hasError={ touched.infobox_structure && errors.infobox_structure }
						setFieldTouched={ setFieldTouched }
						handleChange={ e => {
							// We've changed the infobox structure, so set that we have
							// unsaved state before passing on to default handleChange.
							setHasUnsavedState(true);
							return handleChange(e);
						} }
						initialValue={ initialValues.infobox_structure }
						value={ values.infobox_structure }
					/>
				</FormModal>
			) }
		</Formik>
	);
}
