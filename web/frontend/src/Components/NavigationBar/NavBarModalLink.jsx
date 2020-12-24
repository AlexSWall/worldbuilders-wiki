import React, { useState } from 'react';

import NavBarLink from './NavBarLink';

import Modal from '../Modal';

export default function NavBarModalLink({ linkText, ChildComponent })
{
	const [isModalOpen, setModalOpen] = useState(false);
	const [ModalComponent, setModalComponent] = useState(() => ChildComponent);

	return (
		<>
			<NavBarLink
				onClick={ () => setModalOpen(true) }
				text={ linkText }
				active={ false }
			/>

			<Modal isOpen={ isModalOpen } setOpen = { setModalOpen }>
				<ModalComponent closeModal={ () => setModalOpen(false) } setModalComponent={ setModalComponent } />
			</Modal>
		</>
	);
}
