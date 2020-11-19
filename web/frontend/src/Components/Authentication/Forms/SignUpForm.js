import React, { Component } from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormLink from './../Form Components/AuthFormLink';
import AuthFormCheckBox from './../Form Components/AuthFormCheckBox';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

class SignUpForm extends Component 
{
	render() {
		let {formType, csrfHTML, oldValues, errors} = this.props.formProperties;
		return (
			<form action='Sign_Up' method='post' autoComplete='off'>
				<AuthFormTextEntry 
					formId='preferred_name' 
					labelText='Preferred Name'
					type='text'
					placeholder=''
					oldValue={oldValues.preferred_name}
					errors={errors.preferred_name}
				/>
				<AuthFormTextEntry 
					formId='username' 
					labelText='Username*'
					type='text'
					placeholder=''
					oldValue={oldValues.username}
					errors={errors.username}
				/>
				<AuthFormTextEntry 
					formId='email' 
					labelText='Email Address*'
					type='text'
					placeholder='you@domain.com'
					oldValue={oldValues.email}
					errors={errors.email}
				/>
				<AuthFormTextEntry 
					formId='password' 
					labelText='Password*'
					type='password'
					placeholder=''
					oldValue={oldValues.password}
					errors={errors.password}
				/>
				<AuthFormTextEntry 
					formId='password_confirm' 
					labelText='Confirm Password*'
					type='password'
					placeholder=''
					oldValue={oldValues.password_confirm}
					errors={errors.password_confirm}
				/>
				<AuthFormSubmitButton text='Sign Up' />

				<CSRFField csrfHTML={csrfHTML}/>
			</form>
		);
	}
}

export default SignUpForm;