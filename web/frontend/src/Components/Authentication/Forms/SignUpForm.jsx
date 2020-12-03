import React from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

export default function SignUpForm({ oldValues, errors })
{
	const {
		preferred_name: prevPrefName,
		username: prevUsername,
		email: prevEmail,
		password_new: prevPass,
		password_new_confirm: prevPassConf
	} = oldValues;

	const {
		preferred_name: prevPrefNameError,
		username: prevUsernameError,
		email: prevEmailError,
		password_new: prevPassError,
		password_new_confirm: prevPassConfError
	} = errors;

	return (
		<form action='Sign_Up' method='post' autoComplete='off'>
			<AuthFormTextEntry 
				formId='preferred_name' 
				labelText='Preferred Name'
				type='text'
				placeholder=''
				oldValue={ prevPrefName }
				errors={ prevPrefNameError }
			/>
			<AuthFormTextEntry 
				formId='username' 
				labelText='Username*'
				type='text'
				placeholder=''
				oldValue={ prevUsername }
				errors={ prevUsernameError }
			/>
			<AuthFormTextEntry 
				formId='email' 
				labelText='Email Address*'
				type='text'
				placeholder='you@domain.com'
				oldValue={ prevEmail }
				errors={ prevEmailError }
			/>
			<AuthFormTextEntry 
				formId='password' 
				labelText='Password*'
				type='password'
				placeholder=''
				oldValue={ prevPass }
				errors={ prevPassError }
			/>
			<AuthFormTextEntry 
				formId='password_confirm' 
				labelText='Confirm Password*'
				type='password'
				placeholder=''
				oldValue={ prevPassConf }
				errors={ prevPassConfError }
			/>
			<AuthFormSubmitButton text='Sign Up' />

			<CSRFField />
		</form>
	);
}
