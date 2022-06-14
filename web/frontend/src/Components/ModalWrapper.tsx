import React, { ReactElement, useState } from 'react';

import { Modal } from './Modal'

import useStateWithGetter from 'utils/hooks/useStateWithGetter'

export interface ModalComponentProps
{
	closeModal: () => void;
	setModalComponent: React.Dispatch<React.SetStateAction<(props: ModalComponentProps) => ReactElement>>;
	setHasUnsavedState: React.Dispatch<React.SetStateAction<boolean>>;
};

interface Props
{
	ModalComponent: (props: ModalComponentProps) => ReactElement;
	modalOpenState: [boolean, React.Dispatch<React.SetStateAction<boolean>>];
};

export const ModalWrapper = ({ ModalComponent, modalOpenState }: Props): ReactElement =>
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
};
