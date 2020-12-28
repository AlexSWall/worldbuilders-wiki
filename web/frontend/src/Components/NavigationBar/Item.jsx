import React, { useState } from 'react';

import Modal from '../Modal';

export default function Item({ text, href, onClick, ModalComponent, type='navbar', children })
{
	if ( ModalComponent )
	{
		const [isModalOpen, setModalOpen] = useState(false);
		const [CurrentModalComponent, setModalComponent] = useState(() => ModalComponent);

		const modalComponent = (
			<Modal
				isOpen={ isModalOpen }
				setOpen = { setModalOpen }
			>
				<CurrentModalComponent
					closeModal={ () => setModalOpen(false) }
					setModalComponent={ setModalComponent }
				/>
			</Modal>
		);

		return getItemComponent( type, text, { onClick: () => setModalOpen(true) }, children, modalComponent );
	}
	else if ( onClick )
		return getItemComponent( type, text, { onClick: onClick }, children );
	else if ( href ) 
		return getItemComponent( type, text, { href: href }, children );
	else if ( href === false )
		return getItemComponent( type, text, { onClick: () => { return false; } }, children );
	else
	{
		href = '/#' + text.replace(/[^a-zA-Z]+/g, '-').toLowerCase();
		return getItemComponent( type, text, { href: href }, children );
	}
}

function getItemComponent( type, text, action, children, extraComponent=undefined )
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
