
export type QuickNavigatorEntries = {[quickNavigator: string]: QuickNavigatorEntry[]};

export type QuickNavigatorEntry =
	| {
		'type': 'folder';
		'displayName': string;
		'value': QuickNavigatorEntry[];
	} | {
		'type': 'wikipage';
		'displayName': string;
		'value': string;
	} | {
		'type': 'category';
		'displayName': string;
		'value': string;
	};

export type ProcessedQuickNavigatorEntries = {
	'id': string;
	'type': 'top-level';
	'value': ProcessedQuickNavigatorEntry[];
}

export type ProcessedQuickNavigatorEntry =
	| ProcessedQuickNavigatorFolderEntry
	| ProcessedQuickNavigatorWikipageEntry
	| ProcessedQuickNavigatorCategoryEntry;

export type ProcessedQuickNavigatorFolderEntry = {
	'id': string;
	'type': 'folder';
	'displayName': string;
	'value': ProcessedQuickNavigatorEntry[];
};

export type ProcessedQuickNavigatorWikipageEntry = {
	'id': string;
	'type': 'wikipage';
	'displayName': string;
	'value': string;
};

export type ProcessedQuickNavigatorCategoryEntry = {
	'id': string;
	'type': 'category';
	'displayName': string;
	'value': string;
};
