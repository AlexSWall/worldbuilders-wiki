import React, { Component } from 'react';

class NavBarDropdownItem extends Component 
{	
	render() {
		return (
			<a href={this.props.href}>{this.props.text}</a>
		);
	}
}

export default NavBarDropdownItem;