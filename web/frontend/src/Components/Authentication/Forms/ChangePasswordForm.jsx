import React from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

export default function ChangePasswordForm({ csrfHTML, oldValues, errors })
{
	const {
		password_old: prevOldPass,
		password_new: prevNewPass,
		password_new_confirm: prevNewPassConf
	} = oldValues;

	const {
		password_old: prevOldPassError,
		password_new: prevNewPassError,
		password_new_confirm: prevNewPassConfError
	} = errors;

	return (
		<form action='Change_Password' method='post' autoComplete='off'>
			<AuthFormTextEntry 
				formId='password_old' 
				labelText='Current Password'
				type='password'
				placeholder=''
				oldValue={ prevOldPass }
				errors={ prevOldPassError }
			/>
			<AuthFormTextEntry 
				formId='password_new' 
				labelText='New Password'
				type='password'
				placeholder=''
				oldValue={ prevNewPass }
				errors={ prevNewPassError }
			/>
			<AuthFormTextEntry 
				formId='password_new_confirm' 
				labelText='Confirm New Password'
				type='password'
				placeholder=''
				oldValue={ prevNewPassConf }
				errors={ prevNewPassConfError }
			/>
			<AuthFormSubmitButton text='Change Password' />

			<CSRFField csrfHTML={ csrfHTML }/>
		</form>
	);
}
