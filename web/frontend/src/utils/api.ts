import { FormikErrors } from "formik";

import { CsrfTokens } from "GlobalState";


// Exported as this type is needed for caching this API call.
export type WikiPageData = {
	title: string;
	urlPath: string;
	html: string;
};

type WikiPagePath = string;
type InfoboxName = string;

export type ApiGetWikiPage = {
	path: `/w/${WikiPagePath}`;
	data: {
		wikiPage: WikiPageData;
		error?: string;
	};
};

export type ApiGetWikiText = {
	path: `/a/wiki?wikipage=${WikiPagePath}`;
	data: {
		wikitext: string;
		error?: string;
	};
};

export type ApiGetInfoboxNames = {
	path: `/a/infobox`;
	data: {
		infobox_names: string[];
		error?: string;
	};
};

export type ApiGetInfobox = {
	path: `/a/infobox?infobox=${InfoboxName}`;
	data: {
		infobox_structure_text: string;
		error?: string;
	};
};

export type ApiGetType =
	| ApiGetWikiPage
	| ApiGetWikiText
	| ApiGetInfoboxNames
	| ApiGetInfobox;

export async function makeApiGetRequest<T extends ApiGetType>(
	path: T['path'],
	successCallback: ( data: T['data'] ) => void,
	failureCallback: | ( ( result: Response, data: T['data'] ) => void ) | null = null,
	allow404: boolean = false
): Promise<void>
{
	console.log(`Making GET Request to '${path}'`)

	try
	{
		const res = await fetch( path, {
			method: 'get',
			headers: {
				'Accept': 'application/json, text/plain, */*',
			}
		} );

		const contentType = res.headers.get( 'content-type' );
		const isJson = !!(contentType && contentType.indexOf( 'application/json' ) !== -1)

		const data: T['data'] = isJson ? await res.json() : await res.text();

		if ( ! ( allow404 || res.ok ) || ! isJson )
		{
			if ( ! res.ok )
			{
				console.log('Error: Received status code ' + res.status + ' in response to GET request');
			}
			else if ( ! isJson )
			{
				console.log('Error: Did not receive JSON in response to GET request');
			}
			else
			{
				// Here due to failing success predicate
				console.log('Error: Failed success predicate in response to GET request');
			}

			if ( isJson )
			{
				// Check for an 'error' key, and use it if it exists.
				if ( data.error )
				{
					console.log( 'Error: ' + data.error );
				}
				else
				{
					console.log( 'Error: ' + data );
				}
			}
			else
			{
				console.log( 'Error (text): ' + data );
			}

			if ( failureCallback !== null )
			{
				failureCallback( res, data );
			}

			return;
		}

		// We have a successful JSON return; call success callback (if any)...

		if ( successCallback !== null )
		{
			successCallback( data );
		}
	}
	catch( error )
	{
		console.log( 'Exception thrown on making GET request...' )
		console.log( error );
	}
}

type ApiAuthPostType =
	{
		path: '/auth/';
		action: 'sign up';
		data: {
			username: string;
			email: string;
			password: string;
			preferred_name: string;
		};
	} | {
		path: '/auth/';
		action: 'sign in';
		data: {
			identity: string;
			password: string;
			remember_me: boolean;
		};
	} | {
		path: '/auth/';
		action: 'sign out';
		data: {};
	} | {
		path: '/auth/';
		action: 'change password';
		data: {
			password_old: string;
			password_new: string;
		};
	} | {
		path: '/auth/';
		action: 'request password reset email';
		data: {
			email: string;
		};
	} | {
		path: '/auth/';
		action: 'reset password';
		data: {
			email: string;
			identifier: string;
			password_new: string;
		};
	};

type ApiWikiPostType =
	{
		path: '/a/wiki';
		action: 'create';
		data: {
			page_path: string;
			title: string;
		};
	} | {
		path: '/a/wiki';
		action: 'modify';
		data: {
			page_path: string;
			title: string;
			content: string;
		};
	} | {
		path: '/a/wiki';
		action: 'delete';
		data: {
			page_path: string;
		};
	};

type ApiInfoboxPostType =
	{
		path: '/a/infobox';
		action: 'create';
		data: {
			infobox_name: string;
		};
	} | {
		path: '/a/infobox';
		action: 'modify';
		data: {
			infobox_name: string;
			structure: string;
		};
	} | {
		path: '/a/infobox';
		action: 'delete';
		data: {
			infobox_name: string;
		};
	};

type ApiPostType = ApiAuthPostType | ApiWikiPostType | ApiInfoboxPostType;

export async function makeApiPostRequest<T extends ApiPostType>(
	path: T['path'],
	action: T['action'],
	data: T['data'],
	csrfTokens: CsrfTokens,
	successCallback?: ( result: Response ) => void,
	validationFailureCallback?: ( validationErrors: FormikErrors<{[field: string]: string}> ) => void,
	setGenericError?: ( error: string | null ) => void,
	setSubmitting?: ( isSubmitting: boolean ) => void,
): Promise<void>
{
	console.log('Making POST Request to API...')

	// Clear any generic errors for the form; in practice, this will be a call to
	// setSubmissionError.
	setGenericError && setGenericError( null );

	let boundSuccessCallback = null;

	try
	{
		const res = await fetch( path, {
			method: 'post',
			headers: {
				'Accept': 'application/json, text/plain, */*',
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(Object.assign({}, {
				action: action,
				data: data,
			}, csrfTokens))
		});

		if (res.ok)
		{
			// Bind result to success callback to be called at end
			boundSuccessCallback = () => {
				successCallback && successCallback(res);
			}
		}
		else
		{
			console.log('Error: Received status code ' + res.status + ' in response to POST request');

			const contentType = res.headers.get( 'content-type' );

			if ( contentType && contentType.indexOf( 'application/json' ) !== -1 )
			{
				const data = await res.json();

				if ( data.error === 'Validation failure' )
				{
					console.log( 'Validation failure' );

					validationFailureCallback && validationFailureCallback( data.validation_errors );
				}
				else
				{
					console.log( 'Error: ' + data.error );

					setGenericError && setGenericError( data.error );
				}
			}
			else
			{
				const text = await res.text();

				console.log('Error (text): ' + text);

				setGenericError && setGenericError( text );
			}
		}
	}
	catch( error )
	{
		console.log('Failed to make POST request...');
		console.log(error);
	}
	finally
	{
		console.log('Finished submission');

		setSubmitting && setSubmitting(false);

		if ( boundSuccessCallback !== null )
		{
			console.log('Calling success callback after submission');

			boundSuccessCallback();
		}
	}
}
