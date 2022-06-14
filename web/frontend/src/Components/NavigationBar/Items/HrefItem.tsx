import React from 'react';

import Item from './Item';

export default function HrefItem({ text, href=null, type='navbar', children })
{
	if ( href === null )
	{
		href = '/#' + text.replace(/[^a-zA-Z]+/g, '-').toLowerCase();
	}

	return (
		<Item
			text={ text }
			type={ type }
			action={ { href: href } }
			children={ children }
		/>
	);
}
