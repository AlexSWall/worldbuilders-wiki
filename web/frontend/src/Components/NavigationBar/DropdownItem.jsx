import React from 'react';

import Item from './Item';

export default function DropdownItem( args )
{
	return <Item type='dropdown' { ...args } />
}
