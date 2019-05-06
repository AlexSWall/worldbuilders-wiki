import React, { Component } from 'react';

class NavBarList extends Component 
{	
	render() {
		console.log(this.props.position);
		return (
			<ul className={"navbar-list" + (this.props.position ? ` navbar-list-${this.props.position}` : '')}>
				{this.props.children}
			</ul>
		);
	}
}

export default NavBarList;