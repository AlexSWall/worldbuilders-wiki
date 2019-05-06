import React, { Component } from 'react';

import SignInForm from './SignInForm';
import SignUpForm from './SignUpForm';

class AuthFormBody extends Component 
{
	render() {
		return (
			<div className="card-body">
				{
					/* Switch on the type of form required. */
					{
						'Sign In': <SignInForm formProperties={this.props.formProperties} />,
						'Sign Up': <SignUpForm formProperties={this.props.formProperties} />
					}['Sign In']
				}
			</div>
		);
	}
}

export default AuthFormBody;