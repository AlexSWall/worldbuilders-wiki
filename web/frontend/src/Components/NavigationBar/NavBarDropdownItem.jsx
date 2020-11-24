import React from 'react';

export default function NavBarDropdownItem({ href, text })
{	
	return (
		<a href={ href }>{ text }</a>
	);
}
