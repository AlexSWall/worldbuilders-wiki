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
					<NavBarContent authenticationData={this.props.authenticationData}/>
				</div>
			</div>
		);
	}
}

export default NavigationBar;