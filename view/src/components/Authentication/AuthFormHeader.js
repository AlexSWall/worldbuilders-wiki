import React, { Component } from 'react';

class AuthFormHeader extends Component 
{
	render() {
		return (
			<div className="card-header">
				{this.props.text}
			</div>
		);
	}
}

export default AuthFormHeader;