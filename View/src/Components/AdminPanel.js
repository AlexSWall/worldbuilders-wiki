import React, { Component } from 'react';

class AdminPanel extends Component 
{
	render() {
		return (
			<React.Fragment>
				<h1>Admins only!</h1>
				<p>Testing...</p>
				{this.props.children}
			</React.Fragment>
		);
	}
}

export default AdminPanel;