import React, { ReactElement, useState } from 'react';

import { Item } from './Item';
import { ModalProps, ModalWrapper } from '../../ModalWrapper';

interface Props
{
	text: string;
	ModalComponent: (props: ModalProps) => ReactElement;
	type?: 'navbar' | 'dropdown';
	children?: React.ReactNode;
};

export const ModalItem = ({ text, ModalComponent, type='navbar', children }: Props): ReactElement =>
{
	// We need to own the 'modal open' state and pass it to the ModalWrapper as
	// we need to have access to setModalOpen to be able to initially open it.
	const [ isModalOpen, setModalOpen ] = useState(false);

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
};
