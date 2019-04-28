import React, { Component } from 'react';

class NavBarItem extends Component 
{
	render() {
		return (
			<li className="navbar-item">
				{this.props.children}
			</li>
		);
	}
}

export default NavBarItem;