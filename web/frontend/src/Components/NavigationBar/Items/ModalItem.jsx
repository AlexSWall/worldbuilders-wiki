import React, { useState } from 'react';

import Item from './Item';
import ModalWrapper from '../../ModalWrapper';

export default function ModalItem({ text, ModalComponent, type='navbar', children })
{
	// We need to own the 'modal open' state and pass it to the ModalWrapper as
	// we neet to have access to setModalOpen to be able to initially open it.
	const [isModalOpen, setModalOpen] = useState(false);

	const modalComponent = (
		<ModalWrapper
			ModalComponent={ ModalComponent }
			modalOpenState={ [isModalOpen, setModalOpen] }
		/>
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
