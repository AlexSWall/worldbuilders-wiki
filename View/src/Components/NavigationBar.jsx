import React, { Component } from 'react';

import NavBarBrand   from './NavigationBar/NavBarBrand';
import NavBarContent from './NavigationBar/NavBarContent';

class NavigationBar extends Component 
{
	render() {
		return (
			<div id="navbarWrapper">
				<div id="navbar">
					<NavBarBrand />
					<NavBarContent auth={this.props.auth}/>
				</div>
			</div>
		);
	}
}

export default NavigationBar;