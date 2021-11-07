import React from 'react';

import Item from './Item';

export default function OnClickItem({ text, onClick, type='navbar', children })
{
	return (
		<Item
			text={ text }
			type={ type }
			action={ { onClick: onClick } }
			children={ children }
		/>
	);
}
