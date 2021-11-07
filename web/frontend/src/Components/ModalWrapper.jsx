import React, { useState } from 'react';

import Modal from './Modal'

export default function ModalWrapper({ ModalComponent, modalOpenState })
{
	const [isModalOpen, setModalOpen] = modalOpenState;
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

	return modalComponent;
}

