import React, { Component } from 'react';

import AuthFormTextEntry from './../Form Components/AuthFormTextEntry';
import AuthFormSubmitButton from './../Form Components/AuthFormSubmitButton';
import CSRFField from 'Form Components/CSRFField'

class PasswordRecoveryForm extends Component 
{
	render() {
		let {formType, csrfHTML, oldValues, errors} = this.props.formProperties;
		return (
			<form action='Password_Recovery' method='post' autoComplete='off'>
				<AuthFormTextEntry 
					formId='email' 
					labelText='Email Address*'
					type='text'
					placeholder='you@domain.com'
					oldValue={oldValues.email}
					errors={errors.email}
				/>
				<AuthFormSubmitButton text='Send Recovery Email' />

				<CSRFField csrfHTML={csrfHTML}/>
			</form>
		);
	}
}

export default PasswordRecoveryForm;