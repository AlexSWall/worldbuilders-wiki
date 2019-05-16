import React, { Component } from 'react';

import AuthFormHeader from './AuthFormHeader'
import AuthFormBody from './AuthFormBody'

class AuthForm extends Component 
{
	render() {
		return (
			<div className="card">
				<AuthFormHeader text={this.props.formProperties.title} />
				<AuthFormBody formProperties={this.props.formProperties} />
			</div>
		);
	}
}

export default AuthForm;