import React from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormLink from './../Form Components/AuthFormLink';
import AuthFormCheckBox from './../Form Components/AuthFormCheckBox';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

export default function SignInForm({ csrfHTML, oldValues, errors })
{
	const {
		identity: prevIdentity,
		password: prevPass
	} = oldValues;

	const {
		identity: prevIdentityError,
		password: prevPassError
	} = errors;

	return (
		<form action='Sign_In' method='post' autoComplete='off'>
			<AuthFormTextEntry 
				formId='identity'
				labelText='Username or Email'
				type='text'
				placeholder='you@domain.com'
				oldValue={ prevIdentity }
				errors={ prevIdentityError }
			/>
			<AuthFormTextEntry 
				formId='password' 
				labelText='Password'
				type='password'
				placeholder=''
				oldValue={ prevPass }
				errors={ prevPassError }
			/>
			<AuthFormLink
				target='/Password_Recovery'
				text='Forgotten Password'
			/>
			<AuthFormCheckBox
				formId='remember'
				text='Remember Me'
			/>
			<AuthFormSubmitButton text='Sign In' />

			<CSRFField csrfHTML={ csrfHTML }/>
		</form>
	);
}
