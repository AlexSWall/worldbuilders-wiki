import React, { Component } from 'react';

class AuthFormLink extends Component 
{
	render() {
		return (
			<div className='form-group'>
				<a href={this.props.target}>{this.props.text}</a>
			</div>
		);
	}
}

export default AuthFormLink;