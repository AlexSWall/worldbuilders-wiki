import React, { Component } from 'react';

class AuthFormSubmitButton extends Component 
{
	render() {
		return (
			<button type='submit' className='btn btn-default'>{this.props.text}</button>
		);
	}
}

export default AuthFormSubmitButton;