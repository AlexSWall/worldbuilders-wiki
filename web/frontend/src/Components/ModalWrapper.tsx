import React, { useState } from 'react';

import Modal from './Modal'

import useStateWithGetter from 'utils/hooks/useStateWithGetter.js'

export default function ModalWrapper({ ModalComponent, modalOpenState })
{
	// State caught from the caller; the caller needs to create the state so that
	// it can access the 'setModalOpen' function, needed to be able to initially
	// open the modal.
	const [isModalOpen, setModalOpen] = modalOpenState;

	// Used to change the modal component to a different modal component from
	// within the first modal component.
	const [CurrentModalComponent, setModalComponent] = useState(() => ModalComponent);

	// Used to determine whether to give an additional prompt before closing a
	// modal, to avoid accidentally losing unsaved state.
	const [ , getHasUnsavedState, setHasUnsavedState] = useStateWithGetter(false);

	// Create and return the Modal containing the 'current' modal component
	// stored in our state.
	return (
		<Modal
			isOpen={ isModalOpen }
			setOpen={ setModalOpen }
			getHasUnsavedState={ getHasUnsavedState }
		>
			<CurrentModalComponent
				closeModal={ () => setModalOpen(false) }
				setModalComponent={ setModalComponent }
				setHasUnsavedState={ setHasUnsavedState }
			/>
		</Modal>
	);
}

