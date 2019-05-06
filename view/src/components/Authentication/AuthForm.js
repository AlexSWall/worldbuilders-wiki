import React, { Component } from 'react';

import AuthFormHeader from './AuthFormHeader'
import AuthFormBody from './AuthFormBody'

class AuthForm extends Component 
{
	render() {
		return (
			<div className="card">
				<AuthFormHeader text='Sign In' />
				<AuthFormBody
					formProperties={this.props.formProperties}
					csrfField={this.props.csrfField}
				/>
			</div>
		);
	}
}

export default AuthForm;