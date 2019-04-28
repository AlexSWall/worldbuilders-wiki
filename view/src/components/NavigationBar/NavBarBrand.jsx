import React, { Component } from 'react';

class NavBarBrand extends Component 
{
	render() {
		return (
			<div id="navbar-brand-wrapper">
				<a id="navbar-brand" href="{{ path_for('home') }}">
					My Website
				</a>
			</div>
		);
	}
}

export default NavBarBrand;

