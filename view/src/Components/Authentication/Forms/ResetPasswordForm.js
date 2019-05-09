import React, { Component } from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from './../Form Components/CSRFField';

class ResetPasswordForm extends Component 
{
	render() {
		let {formType, oldValues, errors, email, identifier} = this.props.formProperties;
		let csrfField = this.props.csrfField;
		return (
			<form action={`Reset_Password?email=${email}&identifier=${identifier}`} method='post' autoComplete='off'>
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

				<CSRFField csrfField={csrfField}/>
			</form>
		);
	}
}

export default ResetPasswordForm;