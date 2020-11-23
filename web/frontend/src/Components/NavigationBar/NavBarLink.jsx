import React, { Component } from 'react';
import NavBarItem from './NavBarItem';

class NavBarLink extends Component 
{	
	render() {
		const action = (this.props.onClick == null)
			? { href: this.props.href }
			: { onClick: this.props.onClick };

		return (
			<NavBarItem>
				<a className={this.props.active ? 'active' : undefined}
				{...action}>
					{this.props.text}
				</a>
			</NavBarItem>
		);
	}
}

export default NavBarLink;
