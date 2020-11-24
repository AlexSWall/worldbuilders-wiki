import React from 'react';

export default function NavBarDropdown({ active, href, text, children })
{	
	return (
		<li className={ "navbar-item navbar-dropdown" + (active ? " active" : "") }>
			<a className="navbar-dropbtn" href={ href } onClick={ () => { return false; } }>{ text }</a>
			<div className="dropdown-content">
				{children}
			</div>
		</li>
	);
}
