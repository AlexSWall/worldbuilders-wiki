import React, { useState } from 'react';

import NavBarLink from '../NavBarLink';

import Modal from '../../Modal';

import WikiPageCreationForm from './WikiPageCreationForm'

export default function CreateWikiPageNavBarLink()
{
	const [isModalOpen, setModalOpen] = useState(false);

	return (
		<>
			<NavBarLink
				onClick={ () => setModalOpen(true) }
				text="Add Page"
				active={ false }
			/>

			<Modal isOpen={ isModalOpen } setOpen = { setModalOpen }>
				<WikiPageCreationForm closeModal={ () => setModalOpen(false) }/>
			</Modal>
		</>
	);
}
