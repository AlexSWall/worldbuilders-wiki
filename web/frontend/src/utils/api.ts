export async function makeApiPostRequest(path, action, data, csrfTokens, successCallback, validationFailureCallback, setGenericError, setSubmitting )
{
	console.log('Making POST Request to API...')

	// Clear any generic errors for the form; in practice, this will be a call to
	// setSubmissionError.
	setGenericError( null );

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
				successCallback(res);
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

					validationFailureCallback( data.validation_errors );
				}
				else
				{
					console.log( 'Error: ' + data.error );

					setGenericError( data.error );
				}
			}
			else
			{
				const text = await res.text();

				console.log('Error (text): ' + text);

				genericFailureCallback( text );
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

		setSubmitting(false);

		if ( boundSuccessCallback !== null )
		{
			console.log('Calling success callback after submission');

			boundSuccessCallback();
		}
	}
}

export async function makeApiGetRequest(path, successPredicate, successCallback, failureCallback, allow404 = false )
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
		const isJson = contentType && contentType.indexOf( 'application/json' ) !== -1

		const data = isJson ? await res.json() : await res.text();

		if ( ! ( allow404 || res.ok ) || ! isJson || ( successPredicate !== null && ! successPredicate( res, data ) ) )
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
			successCallback( res, data );
		}
	}
	catch( error )
	{
		console.log( 'Exception thrown on making GET request...' )
		console.log( error );
	}
}
