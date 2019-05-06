import React, { Component } from 'react';

class AuthenticationPanel extends Component 
{
	render() {
		return (
			<React.Fragment>
				{this.props.children}
			</React.Fragment>
		);
	}
}

export default AuthenticationPanel;