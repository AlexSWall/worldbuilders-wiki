import React, { ReactElement, useContext, useEffect, useState } from 'react';

import useStateInitiallyNull from 'utils/hooks/useStateInitiallyNull';

import { GlobalStateContext } from 'GlobalState';

import { QuickNavigator } from './QuickNavigator';
import { QuickNavigatorEntries, QuickNavigatorEntry } from './QuickNavigatorTypes';

import { SelectDropdown } from 'Components/Form_Components/SelectDropdown';
import { ErrorLabel } from 'Components/Form_Components/ErrorLabel';

import { ApiGetQuickNavigator, makeApiGetRequest, makeApiPostRequest }  from 'utils/api';
import { returnNotEmpty } from 'utils/types';

export default function QuickNavigatorController(): ReactElement
{
	const globalState = useContext( GlobalStateContext );

	const [ quickNavigationData, setQuickNavigationData ] = useStateInitiallyNull<QuickNavigatorEntries>();

	const [ quickNavigatorSelected, setQuickNavigatorSelected ] = useState<string | null>( null );

	const [ submissionError, setSubmissionError ] = useState<string | null>( null );

	const load = () =>
	{
		console.log('Loading the Quick Navigator data...');

		makeApiGetRequest<ApiGetQuickNavigator>(
			`/u/quick-navigator`,
			data =>
				{
					const quickNavigatorData = data.json_data;

					console.log( 'Entries retrieved from API...', quickNavigatorData );

					setQuickNavigationData( quickNavigatorData );
				}
		);
	}

	const save = () =>
	{
		console.log('Saving the Quick Navigator data...');

		setSubmissionError( null );

		makeApiPostRequest(
			'/u/quick-navigator',
			'set',
			{
				json_data: quickNavigationData
			},
			globalState.csrfTokens,
			() => {  // Success
			},
			// Validation Failure callback
			( errors ) => {
				if ( errors['json_data'] !== undefined )
					setSubmissionError( errors['json_data'] );
				else
					setSubmissionError( 'Received errors but failed to find expected error field.' );
			},
			setSubmissionError,  // Set Generic Error
		);
	}

	// On the initial load, obtain the quick navigation to populate the select
	// dropdown.
	useEffect( load, []);

	// setQuickNavigationData({
	// 	'Characters': [
	// 		{
	// 			type: 'wikipage',
	// 			name: 'Home',
	// 			value: ''
	// 		},
	// 		{
	// 			type: 'folder',
	// 			name: 'Player Characters',
	// 			value: [
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Bael',
	// 					value: 'baelathazah-locksmith'
	// 				},
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Nyma',
	// 					value: 'nyma-keryth'
	// 				}
	// 			]
	// 		},
	// 		{
	// 			type: 'folder',
	// 			name: 'NPCs',
	// 			value: [
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Tadrah',
	// 					value: 'tadrah-thicklema'
	// 				},
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Thoros',
	// 					value: 'thoros'
	// 				}
	// 			]
	// 		}
	// 	],
	// 	'World Building': [
	// 		{
	// 			type: 'folder',
	// 			name: 'Deities',
	// 			value: [
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Avahra',
	// 					value: 'avahra'
	// 				},
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Bahamut',
	// 					value: 'bahamut'
	// 				}
	// 			]
	// 		},
	// 		{
	// 			type: 'folder',
	// 			name: 'Places',
	// 			value: [
	// 				{
	// 					type: 'wikipage',
	// 					name: 'Clearwood Grove',
	// 					value: 'clearwood-grove'
	// 				},
	// 				{
	// 					type: 'wikipage',
	// 					name: 'The Ferasav Crossroad',
	// 					value: 'the-ferasav-crossroads'
	// 				}
	// 			]
	// 		}
	// 	]
	// });

	if ( quickNavigationData === null )
	{
		return ( <i> Fetching and loading Quick Navigator... </i> );
	}

	let selectedQuickNavigatorData: QuickNavigatorEntry[] | null = null;

	if ( quickNavigatorSelected !== null )
	{
		selectedQuickNavigatorData = returnNotEmpty( quickNavigationData[quickNavigatorSelected] );
	}

	return (
		<div className='form'>
			<SelectDropdown
				formId='select_quick_navigator'
				labelText='Quick Navigator'
				width={ 250 }
				hasError= { false }
				setValue={ setQuickNavigatorSelected }
				options={ Object.keys( quickNavigationData ) }
				initialValue={ quickNavigatorSelected }
			/>

			{ ( quickNavigatorSelected === null || selectedQuickNavigatorData === null )
				? ( 
					<div className='form-group'>
						<i> Please select a Quick Navigator... </i> )
					</div>
				) : (
					<QuickNavigator
						selectedQuickNavigatorData={ selectedQuickNavigatorData }
						setSelectedQuickNavigatorData={ data =>
							{
								const newQuickNavigationData = structuredClone( quickNavigationData );
								newQuickNavigationData[ quickNavigatorSelected ] = data;
								setQuickNavigationData( newQuickNavigationData );
							}
						}
					/>
				)
			}

			<button onClick={ () => {
					load();
				} }>
				{ 'Load' }
			</button>

			<button onClick={ () => {
					save();
				} }>
				{ 'Save' }
			</button>

			{ submissionError && (
				<ErrorLabel width={ 250 }> { submissionError } </ErrorLabel>
			) }
		</div>
	);
}
