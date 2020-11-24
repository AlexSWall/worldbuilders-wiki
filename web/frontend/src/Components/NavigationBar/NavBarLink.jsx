import React from 'react';

import NavBarItem from './NavBarItem';

export default function NavBarLink({ onClick, href, active, text })
{	
	const action = (onClick == null)
		? { href: href }
		: { onClick: onClick };

	return (
		<NavBarItem>
			<a className={ active ? 'active' : undefined }
			{ ...action }>
				{ text }
			</a>
		</NavBarItem>
	);
}
