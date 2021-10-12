export async function makeApiPostRequest(path, action, data, csrfTokens, successCallback, validationFailureCallback, setGenericError, setSubmitting,  )
{
	console.log('Making POST Request to API...')

	// Clear any generic errors for the form; in practice, this will be a call to
	// setSubmissionError.
	setGenericError( null );

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
			successCallback( res );
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
		console.log('Failed to make POST request...')
		console.log(error);
	}
	finally
	{
		console.log('Finished submission')

		setSubmitting(false);
	}
}
