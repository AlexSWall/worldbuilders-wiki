import React, { useState } from 'react';

import Item from './Item';
import Modal from '../../Modal';

export default function ModalItem({ text, ModalComponent, type='navbar', children })
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

	return (
		<Item
			text={ text }
			type={ type }
			action={ { onClick: () => setModalOpen(true) } }
			extraComponent={ modalComponent }
			children={ children }
		/>
	);
}
