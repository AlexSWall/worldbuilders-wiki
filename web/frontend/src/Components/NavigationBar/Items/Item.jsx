import React from 'react';

/**
 * type is currently either 'navbar' or 'dropdown'
 */
export default function Item({ text, type, action, children, extraComponent=undefined })
{
	return (
		<li className={ type + '-item' }>
			<a className={ type + '-link underline-right' }
			{ ...action }>
				{ text }
			</a>
			{ extraComponent }
			{ children }
		</li>
	);
}
