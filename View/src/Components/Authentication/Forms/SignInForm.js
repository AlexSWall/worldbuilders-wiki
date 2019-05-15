import React, { Component } from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormLink from './../Form Components/AuthFormLink';
import AuthFormCheckBox from './../Form Components/AuthFormCheckBox';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from './../Form Components/CSRFField';

class SignInForm extends Component 
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

export default SignInForm;