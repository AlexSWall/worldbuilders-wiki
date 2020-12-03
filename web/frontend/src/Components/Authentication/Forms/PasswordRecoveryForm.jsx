import React from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

export default function PasswordRecoveryForm({ oldValues, errors })
{
	const { email: prevEmail } = oldValues;
	const { email: prevEmailError } = errors;

	return (
		<form action='Password_Recovery' method='post' autoComplete='off'>
			<AuthFormTextEntry 
				formId='email' 
				labelText='Email Address*'
				type='text'
				placeholder='you@domain.com'
				oldValue={ prevEmail }
				errors={ prevEmailError }
			/>
			<AuthFormSubmitButton text='Send Recovery Email' />

			<CSRFField />
		</form>
	);
}
