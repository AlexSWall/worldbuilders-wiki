import React from 'react';

import AuthFormHeader from './AuthFormHeader'
import AuthFormBody from './AuthFormBody'

export default function AuthForm({ formProperties })
{
	return (
		<div className="card">
			<AuthFormHeader text={ formProperties.title } />
			<AuthFormBody formProperties={ formProperties } />
		</div>
	);
}
