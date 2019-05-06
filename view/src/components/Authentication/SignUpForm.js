import React, { Component } from 'react';

import AuthFormTextEntry from './AuthFormTextEntry';
import AuthFormLink from './AuthFormLink';
import AuthFormCheckBox from './AuthFormCheckBox';
import AuthFormSubmitButton from './AuthFormSubmitButton';
import CSRFField from './CSRFField';

class SignUpForm extends Component 
{
	render() {
		let {formType, oldValues, errors} = this.props.formProperties;
		let csrfField = this.props.csrfField;
		return (
			<form action='Sign_In' method='post' autoComplete='off'>
				<AuthFormTextEntry 
					formId='identity' 
					labelText='Username or Email'
					type='text'
					placeholder='you@domain.com'
					oldValue={oldValues.identity}
					errors={errors.identity}
				/>
				<AuthFormTextEntry 
					formId='password' 
					labelText='Password'
					type='password'
					placeholder=''
					oldValue={oldValues.password}
					errors={errors.password}
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

				<CSRFField csrfField={csrfField}/>
			</form>
		);
	}
}

export default SignUpForm;