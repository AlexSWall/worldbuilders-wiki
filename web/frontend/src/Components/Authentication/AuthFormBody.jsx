import React from 'react';

import SignInForm from './Forms/SignInForm';
import SignUpForm from './Forms/SignUpForm';
import ChangePasswordForm from './Forms/ChangePasswordForm';
import PasswordRecoveryForm from './Forms/PasswordRecoveryForm';
import ResetPasswordForm from './Forms/ResetPasswordForm';

export default function AuthFormBody({ formProperties })
{
	return (
		<div className="card-body">
			{
				/* Switch on the type of form required. */
				{
					'Sign In':           <SignInForm           { ...formProperties } />,
					'Sign Up':           <SignUpForm           { ...formProperties } />,
					'Change Password':   <ChangePasswordForm   { ...formProperties } />,
					'Password Recovery': <PasswordRecoveryForm { ...formProperties } />,
					'Reset Password':    <ResetPasswordForm    { ...formProperties } />
				}[formProperties.formType]
			}
		</div>
	);
}
