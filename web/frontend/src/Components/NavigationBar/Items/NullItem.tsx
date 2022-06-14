import React from 'react';

import Item from './Item';

export default function NullItem({ text, type='navbar', children })
{

	return (
		<Item
			text={ text }
			type={ type }
			action={ { onClick: () => { return false; } } }
			children={ children }
		/>
	);
}
