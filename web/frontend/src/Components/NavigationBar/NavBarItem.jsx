import React from 'react';

export default function NavBarItem({ children })
{
	return (
		<li className="navbar-item">
			{ children }
		</li>
	);
}
