import React, { ReactElement } from 'react';

import { ProcessedQuickNavigatorEntries, ProcessedQuickNavigatorEntry, QuickNavigatorEntry } from './QuickNavigatorTypes';

interface Props
{
	selectedQuickNavigatorData: QuickNavigatorEntry[];
	setSelectedQuickNavigatorData: React.Dispatch<QuickNavigatorEntry[]>;
};

export function QuickNavigator({ selectedQuickNavigatorData, setSelectedQuickNavigatorData }: Props): ReactElement
{
	const quickNavigationSet = preProcessQuickNavigationSet( selectedQuickNavigatorData );setSelectedQuickNavigatorData( postProcessQuickNavigationSet ( quickNavigationSet ) );

	return (
		<React.Fragment />
	);
};

const preProcessQuickNavigationSet = ( selectedQuickNavigatorData: QuickNavigatorEntry[] ):ProcessedQuickNavigatorEntries =>
{
	let idCounter = 1;

	const processEntry = ( entry: QuickNavigatorEntry ): ProcessedQuickNavigatorEntry =>
	{
		const entryId = idCounter;
		idCounter += 1;

		if ( entry.type == 'wikipage' || entry.type == 'category' )
		{
			return {
				'id': `quick-navigation-${entryId}`,
				'type': entry.type,
				'displayName': entry.displayName,
				'value': entry.value
			};
		}

		let values: ProcessedQuickNavigatorEntry[] = [];

		for ( let innerEntry of entry.value )
			values.push( processEntry( innerEntry ) );

		return {
			'id': `quick-navigation-${entryId}`,
			'type': entry.type,
			'displayName': entry.displayName,
			'value': values
		}
	}

	let processedValues: ProcessedQuickNavigatorEntry[] = [];

	for ( let entry of selectedQuickNavigatorData )
		processedValues.push( processEntry( entry ) );

	return {
		id: `quick-navigation-0`,
		type: 'top-level',
		value: processedValues
	};
};

const postProcessQuickNavigationSet = ( selectedQuickNavigatorData: ProcessedQuickNavigatorEntries ): QuickNavigatorEntry[] =>
{
	const processEntry = ( entry: ProcessedQuickNavigatorEntry ): QuickNavigatorEntry =>
	{
		if ( entry.type == 'wikipage' || entry.type == 'category' )
		{
			return {
				'type': entry.type,
				'displayName': entry.displayName,
				'value': entry.value
			};
		}

		let values: QuickNavigatorEntry[] = [];

		for ( let innerEntry of entry.value )
			values.push( processEntry( innerEntry ) );

		return {
			'type': entry.type,
			'displayName': entry.displayName,
			'value': values
		};
	}

	let processedValues: QuickNavigatorEntry[] = [];

	for ( let entry of selectedQuickNavigatorData.value )
		processedValues.push( processEntry( entry ) );

	return processedValues;
};
