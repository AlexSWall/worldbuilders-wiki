import React, { Component } from 'react';

import SignInForm from './Forms/SignInForm';
import SignUpForm from './Forms/SignUpForm';
import ChangePasswordForm from './Forms/ChangePasswordForm';
import PasswordRecoveryForm from './Forms/PasswordRecoveryForm';
import ResetPasswordForm from './Forms/ResetPasswordForm';

class AuthFormBody extends Component 
{
	render() {
		return (
			<div className="card-body">
				{
					/* Switch on the type of form required. */
					{
						'Sign In':           <SignInForm           formProperties={this.props.formProperties} csrfField={this.props.csrfField}/>,
						'Sign Up':           <SignUpForm           formProperties={this.props.formProperties} csrfField={this.props.csrfField}/>,
						'Change Password':   <ChangePasswordForm   formProperties={this.props.formProperties} csrfField={this.props.csrfField}/>,
						'Password Recovery': <PasswordRecoveryForm formProperties={this.props.formProperties} csrfField={this.props.csrfField}/>,
						'Reset Password':    <ResetPasswordForm    formProperties={this.props.formProperties} csrfField={this.props.csrfField}/>
					}[this.props.formProperties.formType]
				}
			</div>
		);
	}
}

export default AuthFormBody;