import React, { useState } from 'react';

import Modal from '../Modal';

export default function NavBarDropdownItem({ text, href, onClick, ModalComponent })
{
	if ( ModalComponent )
	{
		const [isModalOpen, setModalOpen] = useState(false);

		return (
			<>
				<a className="navbar-dropdown-item" onClick={ () => setModalOpen(true) }> { text } </a>

				<Modal isOpen={ isModalOpen } setOpen = { setModalOpen }>
					<ModalComponent closeModal={ () => setModalOpen(false) }/>
				</Modal>
			</>
		);
	}
	else if ( onClick )
	{
		return (
			<a className="navbar-dropdown-item" onClick={ onClick }>{ text }</a>
		);
	}
	else
		return (
			<a className="navbar-dropdown-item" href={ href }>{ text }</a>
		);
}
