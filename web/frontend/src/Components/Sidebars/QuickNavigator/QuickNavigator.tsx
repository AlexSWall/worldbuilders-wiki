import React, { ReactElement } from 'react';

import { DragDropContext, DraggableLocation } from 'react-beautiful-dnd';
import { Droppable, Draggable } from 'react-beautiful-dnd';
import { assertNotEmpty, returnNotEmpty } from 'utils/types';
import { ProcessedQuickNavigatorEntries, ProcessedQuickNavigatorEntry, ProcessedQuickNavigatorFolderEntry, ProcessedQuickNavigatorWikipageEntry, QuickNavigatorEntry } from './QuickNavigatorTypes';

interface Props
{
	selectedQuickNavigatorData: QuickNavigatorEntry[];
	setSelectedQuickNavigatorData: React.Dispatch<QuickNavigatorEntry[]>;
};

export function QuickNavigator({ selectedQuickNavigatorData, setSelectedQuickNavigatorData }: Props): ReactElement
{
	const quickNavigationSet = preProcessQuickNavigationSet( selectedQuickNavigatorData );

	return (
		<DragDropContext
			onDragEnd={({ destination, source }) =>
				{
					if ( !destination ) { return; }
					console.log( 'Destination: ', destination );
					console.log( 'Source: ', source );

					setSelectedQuickNavigatorData( postProcessQuickNavigationSet(
						reorderEntries( quickNavigationSet, source, destination )
					) );
				}}
		>
			<QuickNavContent id={ quickNavigationSet.id } entries={ quickNavigationSet.value }>
			</QuickNavContent>
		</DragDropContext>
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

interface QuickNavContentProps
{
	id: string;
	entries: ProcessedQuickNavigatorEntry[];
	children: React.ReactNode;
};

const QuickNavContent = ( { id, entries, children }: QuickNavContentProps ) => {
	return (
		<Droppable
			droppableId={ `droppable-${id}` }
			type='CARD'
			direction='vertical'
			isCombineEnabled={ false }
			>
			{ dropProvided => (
				<div { ...dropProvided.droppableProps } style={{ padding: '1rem' }}>
					{ children }
					<div style={{ display: 'flex', flexDirection: 'column' }} ref={ dropProvided.innerRef }>
						{ entries.map( (entry, index) => (
							<Draggable key={`draggable-${entry.id}`} draggableId={entry.id} index={index}>
								{dragProvided => (
									<div style={{ padding: '1rem' }}
										{...dragProvided.dragHandleProps}
										{...dragProvided.draggableProps}
										ref={dragProvided.innerRef}
										>
										<QuickNavEntry { ...entry } />
									</div>
								)}
							</Draggable>
						))}
						{ dropProvided.placeholder }
					</div>
				</div>
			) }
		</Droppable>
	);
};

const QuickNavEntry = ( entry: ProcessedQuickNavigatorEntry ) =>
{
	if ( entry.type === 'folder' )
	{
		return <QuickNavFolder { ...entry }/>
	}
	else if ( entry.type === 'wikipage' )
	{
		return <QuickNavWikipage { ...entry }/>
	}
	else if ( entry.type === 'category' )
	{
		return <React.Fragment/>;
	}
	else
	{
		return <React.Fragment/>;
	}
};

const QuickNavFolder = ( { id, displayName, value }: ProcessedQuickNavigatorFolderEntry ) => {
	return (
		<div>
			<QuickNavContent id={ id } entries={ value }>
				<div className='quick-nav-folder'> { displayName } </div>
			</QuickNavContent>
		</div>
	);
};

const QuickNavWikipage = ( { displayName, value }: ProcessedQuickNavigatorWikipageEntry ) => {
	return (
		<a href={ `/#${value}` } className='quick-nav-wikipage'>
			{ displayName }
		</a>
	);
};

export const reorderEntries = (
	quickNavigationSet: ProcessedQuickNavigatorEntries,
	source: DraggableLocation,
	destination: DraggableLocation
): ProcessedQuickNavigatorEntries => {
	let quickNavigationSetCopy = structuredClone( quickNavigationSet );

	console.log( 'Reordering Entries' );
	console.log( quickNavigationSet );
	console.log( source );
	console.log( destination );

	const runOnFolderWithId = (
		entry: ProcessedQuickNavigatorEntries | ProcessedQuickNavigatorFolderEntry,
		folderId: string,
		fn: ( ( folder: ProcessedQuickNavigatorEntries | ProcessedQuickNavigatorFolderEntry ) => void )
	) =>
	{
		if ( `droppable-${entry.id}` === folderId )
		{
			// Found the folder entry; run the function on it and return early.
			fn(entry);
			return true;
		}

		for ( let innerEntry of entry.value )
		{
			// Only run on children folders.
			if ( innerEntry.type == 'folder' )
			{
				// Run recursively on this folder entry; return early if success.
				if ( runOnFolderWithId( innerEntry, folderId, fn ) )
				{
					return true;
				}
			}
		}

		return false;
	}

	if ( source.droppableId === destination.droppableId )
	{
		const success = runOnFolderWithId(
			quickNavigationSetCopy,
			source.droppableId,
			entry => {
				// entry is the parent entry matching source.droppableId; move its
				// source.index-ed value to destination.index by mutable reference.
				entry.value = reorderList( entry.value, source.index, destination.index );
			}
		);

		if ( ! success )
		{
			console.log( 'Failed to find target Droppable to reorder.' );
			return quickNavigationSetCopy;
		}
	}
	else  // source.droppableId !== destination.droppableId
	{
		let targetEntry: ProcessedQuickNavigatorEntry | null = null;

		// Find entry, set targetEntry equal to it, and delete it from source.
		const deleteSuccess = runOnFolderWithId(
			quickNavigationSetCopy,
			source.droppableId,
			entry => {
				// entry is the parent entry matching source.droppableId.

				// Save off the located target entry at index source.index.
				targetEntry = returnNotEmpty<ProcessedQuickNavigatorEntry>( entry.value[source.index] );

				// Delete it from the parent by by mutable reference.
				entry.value.splice(source.index, 1);
			}
		);

		if ( ! deleteSuccess || targetEntry === null )
		{
			console.log( 'Failed to find target Droppable to remove from.' );
			return quickNavigationSetCopy;
		}

		// Insert entry into destination.
		const insertSuccess = runOnFolderWithId(
			quickNavigationSetCopy,
			destination.droppableId,
			entry => {
				// entry is the parent entry matching destination.droppableId.
				assertNotEmpty( targetEntry );

				// Insert targetEntry into entry.value at index destination.index.
				entry.value.splice( destination.index, 0, returnNotEmpty( targetEntry ) );
			}
		);

		if ( ! insertSuccess )
		{
			console.log( 'Failed to find target Droppable to insert into.' );
			return quickNavigationSetCopy;
		}
	}

	// We mutated by reference (if we found the entry).
	return quickNavigationSetCopy;
};

const reorderList = (
	list: ProcessedQuickNavigatorEntry[],
	startIndex: DraggableLocation['index'],
	endIndex: DraggableLocation['index']
): ProcessedQuickNavigatorEntry[] =>
{
	const result = Array.from( list );
	const [ removed ] = result.splice( startIndex, 1 );
	assertNotEmpty( removed );
	result.splice( endIndex, 0, removed );
	return result;
};
