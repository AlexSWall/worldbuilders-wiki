import React from 'react';

export default function Dropdown({ children })
{	
	return (
		<ul className="dropdown-list">
			{children}
		</ul>
	);
}
