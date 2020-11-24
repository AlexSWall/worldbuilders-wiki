import React from 'react';

export default function NavBarList({ position, children })
{	
	return (
		<ul className={ "navbar-list" + (position ? ` navbar-list-${ position }` : '') }>
			{ children }
		</ul>
	);
}
