import React, { Component } from 'react';

class NavBarDropdown extends Component 
{	
	render() {
		return (
			<li className={"navbar-item navbar-dropdown" + (this.props.active ? " active" : "")}>
				<a className="navbar-dropbtn" href={this.props.href} onClick={() => {return false;}}>{this.props.text}</a>
				<div className="dropdown-content">
					{this.props.children}
				</div>
			</li>
		);
	}
}

export default NavBarDropdown;