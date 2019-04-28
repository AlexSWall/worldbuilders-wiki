import React, { Component } from 'react';
import NavBarItem from './NavBarItem';

class NavBarButton extends Component 
{	
	render() {
		return (
			<NavBarItem>
				<a className={this.props.active ? 'active' : undefined}
					href={this.props.href}>
					{this.props.text}
				</a>
			</NavBarItem>
		);
	}
}

export default NavBarButton;