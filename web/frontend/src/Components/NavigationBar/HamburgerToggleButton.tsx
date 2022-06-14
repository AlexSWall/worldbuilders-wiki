import React, { useState } from 'react';

import classNames from 'classnames';

export default function HamburgerToggleButton({ isOnFn })
{
	const [ toggleStateIsOn, setToggleState ] = useState( false );

	return (
		<li className={ 'hamburger-button-item' }>
			<button className={ classNames({ 'hamburger-button': true, 'hamburger-button-on': toggleStateIsOn }) }
				onClick={() => {
					const newToggleStateIsOn = ! toggleStateIsOn;
					setToggleState( newToggleStateIsOn );
					isOnFn( newToggleStateIsOn );
				}}
			>
				<svg width="18" height="14">
					// Draws three lines of width 2px with rounded ends
					<path d="M 0.8,0.8 h16 a0.8,0.8 0 0 1 0,1.6 h-16 a0.8,0.8 0 0 1 0,-1.6 z  M 0.8,6.4 h16 a0.8,0.8 0 0 1 0,1.6 h-16 a0.8,0.8 0 0 1 0,-1.6 z  M 0.8,12 h16 a0.8,0.8 0 0 1 0,1.6 h-16 a0.8,0.8 0 0 1 0,-1.6 z"/>
				</svg>
			</button>
		</li>
	);
}

