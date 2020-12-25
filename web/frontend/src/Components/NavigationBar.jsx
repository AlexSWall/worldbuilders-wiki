import React from 'react';

import NavBarBrand   from './NavigationBar/NavBarBrand';
import NavBarContent from './NavigationBar/NavBarContent';

export default function NavigationBar()
{
	return (
		<div id="navbarWrapper">
			<nav id="navbar">
				<NavBarBrand />
				<NavBarContent />
			</nav>
		</div>
	);
}
