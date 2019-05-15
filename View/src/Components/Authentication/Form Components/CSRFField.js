import React, { Component } from 'react';

class AuthFormSubmitButton extends Component 
{
	render() {
		return (
			<div dangerouslySetInnerHTML={{ __html: this.props.csrfField }} />
		);
	}
}

export default AuthFormSubmitButton;