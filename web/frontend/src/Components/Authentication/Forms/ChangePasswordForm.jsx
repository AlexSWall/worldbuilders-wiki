import React, { Component } from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

class ChangePasswordForm extends Component 
{
	render() {
		let {formType, csrfHTML, oldValues, errors} = this.props.formProperties;
		return (
			<form action='Change_Password' method='post' autoComplete='off'>
				<AuthFormTextEntry 
					formId='password_old' 
					labelText='Current Password'
					type='password'
					placeholder=''
					oldValue={oldValues.password_old}
					errors={errors.password_old}
				/>
				<AuthFormTextEntry 
					formId='password_new' 
					labelText='New Password'
					type='password'
					placeholder=''
					oldValue={oldValues.password_new}
					errors={errors.password_new}
				/>
				<AuthFormTextEntry 
					formId='password_new_confirm' 
					labelText='Confirm New Password'
					type='password'
					placeholder=''
					oldValue={oldValues.password_new_confirm}
					errors={errors.password_new_confirm}
				/>
				<AuthFormSubmitButton text='Change Password' />

				<CSRFField csrfHTML={csrfHTML}/>
			</form>
		);
	}
}

export default ChangePasswordForm;